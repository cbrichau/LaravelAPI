<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use DateTime;
use stdClass;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\APIController;
use App\QueryFilters\API\V1\ProductQueryFilter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends APIController
{
	/**
	 * @OA\Get(
	 *   security={{"sanctum": {}}},
	 *   operationId="downloadLosses",
	 *   tags={"Product"},
	 *   path="/api/v1/products/download-losses",
	 *   summary="Download the losses as a .csv file",
	 *   @OA\Parameter(name="basket_id[is]", in="query"),
	 *   @OA\Parameter(name="product_id[is]", in="query"),
	 *   @OA\Parameter(name="add_to_basket_date[greaterThan]", in="query"),
	 *   @OA\Parameter(name="add_to_basket_date[lowerThan]", in="query"),
	 *   @OA\Parameter(name="removed_from_basket_date[greaterThan]", in="query"),
	 *   @OA\Parameter(name="removed_from_basket_date[lowerThan]", in="query"),
	 *   @OA\Parameter(name="product_price[greaterThan]", in="query"),
	 *   @OA\Parameter(name="product_price[lowerThan]", in="query"),
	 *   @OA\Response(
	 *     response=200,
	 *     description="Downloaded CSV file.",
	 *     @OA\MediaType(
	 *       mediaType="text/csv",
	 *     ),
	 *   ),
	 *   @OA\Response(
	 *     response=401,
	 *     description="Access denied.",
	 *     @OA\JsonContent(
	 *       type="object",
	 *       @OA\Property(property="message", type="string", example="Unauthenticated."),
	 *     ),
	 *   ),
	 *   @OA\Response(
	 *     response=500,
	 *     description="Server error.",
	 *     @OA\JsonContent(
	 *       type="object",
	 *       @OA\Property(property="success", type="bool", example="false"),
	 *       @OA\Property(property="errors", type="object",
	 *         @OA\Property(property="errors", type="array", example={
	 *             "Could not open file",
	 *             "Could not write in file",
	 *           },
	 *           @OA\Items(type="string"),
	 *         ),
	 *       ),
	 *     ),
	 *   ),
	 * )
	 *
	 * Returns a csv file of the products that have been removed from a basket.
	 *
	 * @param Request $request
	 * @return BinaryFileResponse|JsonResponse
	 */
	public function downloadLosses(Request $request)
	{
		$filename = 'losses-' . (new DateTime())->format('Y-m-dTh:i:s') . '.csv';
		$filepath = public_path($filename);

		if (($handle = fopen($filepath, 'w+')) === false)
		{
			return $this->returnErrorResponse(500, ['Could not open file']);
		}

		if (fputcsv($handle, ['basket_id', 'product_id', 'add_to_basket_date', 'removed_from_basket_date', 'basket_checkout_date', 'product_name', 'product_price']) === false)
		{
			return $this->returnErrorResponse(500, ['Could not write in file']);
		}

		$losses = DB::table('basket_product')
			->join('baskets', 'baskets.id', '=', 'basket_id')
			->join('products', 'products.id', '=', 'product_id')
			->select('basket_id', 'product_id', 'basket_product.created_at', 'removal_date', 'checkout_date', 'products.name', 'products.price')
			->whereNotNull('removal_date');

		if (
			is_array($request->query()) &&
			($query = (new ProductQueryFilter())->convertToQuery($request->query())) !== []
		) {
			$losses->where($query);
		}

		/** @var stdClass $loss */
		foreach ($losses->get() as $loss)
		{
			if (
				($json = json_encode($loss)) === false ||
				($data = json_decode($json, true)) === false ||
				(fputcsv($handle, $data)) === false // @phpstan-ignore-line
			) {
				return $this->returnErrorResponse(500, ['Could not write in file']);
			}
		}

		fclose($handle);

		return response()->download($filepath, $filename, ['content-type' => 'text/csv'])->deleteFileAfterSend();
	}
}
