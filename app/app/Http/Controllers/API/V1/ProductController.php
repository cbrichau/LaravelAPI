<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use DateTime;
use stdClass;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\APIController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends APIController
{
	/**
	 * Returns a csv file of the products that have been removed from a basket.
	 *
	 * @param Request $request
	 * @return BinaryFileResponse|JsonResponse
	 */
	public function downloadLosses(Request $request)
	{
		$filename = 'losses-' . (new DateTime())->format('Y-m-dTh:i:s') . '.csv';

		if (($handle = fopen($filename, 'w+')) === false)
		{
			return $this->returnErrorResponse(500, ['Could not open file']);
		}

		if (fputcsv($handle, ['basket_id', 'product_id', 'creation_date', 'removal_date', 'product_name', 'product_price']) === false)
		{
			return $this->returnErrorResponse(500, ['Could not write in file']);
		}

		$losses = DB::table('basket_product')
			->join('baskets', 'baskets.id', '=', 'basket_id')
			->join('products', 'products.id', '=', 'product_id')
			->select('basket_id', 'product_id', 'basket_product.created_at', 'removal_date', 'products.name', 'products.price')
			->whereNotNull('removal_date');

		$filters = $request->query();
		if (isset($filters['productId']))
		{
			$losses->where('product_id', '=', $filters['productId']); //à sécuriser
		}

		/** @var stdClass $loss */
		foreach ($losses->get() as $loss)
		{
			if (
				($json = json_encode($loss)) === false ||
				($data = json_decode($json, true)) === false ||
				(fputcsv($handle, $data)) === false // @phpstan-ignore-line
			)
			{
				return $this->returnErrorResponse(500, ['Could not write in file']);
			}
		}

		fclose($handle);

		return response()->download($filename, $filename, ['content-type' => 'text/csv'])->deleteFileAfterSend();
	}

	// /**
	//  * Display a listing of the resource.
	//  *
	//  * @return \Illuminate\Http\Response
	//  */
	// public function index()
	// {
	// 	//
	// }

	// /**
	//  * Show the form for creating a new resource.
	//  *
	//  * @return \Illuminate\Http\Response
	//  */
	// public function create()
	// {
	// 	//
	// }

	// /**
	//  * Store a newly created resource in storage.
	//  *
	//  * @param  \App\Http\Requests\StoreProductRequest  $request
	//  * @return \Illuminate\Http\Response
	//  */
	// public function store(StoreProductRequest $request)
	// {
	// 	//
	// }

	// /**
	//  * Display the specified resource.
	//  *
	//  * @param  \App\Models\Product  $product
	//  * @return \Illuminate\Http\Response
	//  */
	// public function show(Product $product)
	// {
	// 	//
	// }

	// /**
	//  * Show the form for editing the specified resource.
	//  *
	//  * @param  \App\Models\Product  $product
	//  * @return \Illuminate\Http\Response
	//  */
	// public function edit(Product $product)
	// {
	// 	//
	// }

	// /**
	//  * Update the specified resource in storage.
	//  *
	//  * @param  \App\Http\Requests\UpdateProductRequest  $request
	//  * @param  \App\Models\Product  $product
	//  * @return \Illuminate\Http\Response
	//  */
	// public function update(UpdateProductRequest $request, Product $product)
	// {
	// 	//
	// }

	// /**
	//  * Remove the specified resource from storage.
	//  *
	//  * @param  \App\Models\Product  $product
	//  * @return \Illuminate\Http\Response
	//  */
	// public function destroy(Product $product)
	// {
	// 	//
	// }
}
