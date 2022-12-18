<?php

namespace App\Http\Controllers\API\V1;

use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
	/**
	 * Returns a csv file of the products that have been removed from a basket.
	 *
	 * @return BinaryFileResponse
	 */
	public function downloadLosses(Request $request): BinaryFileResponse
	{
		$filename = 'losses-' . (new DateTime())->format('Y-m-dTh:i:s') . '.csv';

		if (($handle = fopen($filename, 'w+')) === false)
		{
			throw new Exception('Could not open file');
		}

		if (fputcsv($handle, ['basket_id', 'product_id', 'date_removed', 'product_name', 'product_price']) === false)
		{
			throw new Exception('Could not write in file');
		}

		$losses = DB::table('basket_product')
			->join('baskets', 'baskets.id', '=', 'basket_id')
			->join('products', 'products.id', '=', 'product_id')
			->select('basket_id', 'product_id', 'date_removed', 'products.name', 'products.price')
			->whereNotNull('date_removed');

		$filters = $request->query();
		if (isset($filters['productId']))
		{
			$losses->where('product_id', '=', $filters['productId']); //à sécuriser
		}

		/** @var stdClass $loss */
		foreach ($losses->get() as $loss)
		{
			if (fputcsv($handle, json_decode(json_encode($loss), true)) === false)
			{
				throw new Exception('Could not write in file');
			}
		}

		fclose($handle);

		return response()->download($filename, $filename, ['Content-Type' => 'text/csv'])->deleteFileAfterSend();
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
