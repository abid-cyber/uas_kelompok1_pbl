<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Mock products data for testing
     */
    private $products = [
        1 => ['id' => 1, 'name' => 'Product 1', 'price' => 50000, 'stock' => 100],
        2 => ['id' => 2, 'name' => 'Product 2', 'price' => 75000, 'stock' => 50],
        3 => ['id' => 3, 'name' => 'Product 3', 'price' => 100000, 'stock' => 25],
    ];

    /**
     * Get product by ID
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $correlationId = $request->header('X-Correlation-ID');

        if (!isset($this->products[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'correlation_id' => $correlationId,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully',
            'data' => $this->products[$id],
            'correlation_id' => $correlationId,
        ], 200);
    }

    /**
     * Update product stock
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStock(Request $request, $id)
    {
        $correlationId = $request->header('X-Correlation-ID');

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'correlation_id' => $correlationId,
            ], 422);
        }

        if (!isset($this->products[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'correlation_id' => $correlationId,
            ], 404);
        }

        // Update stock (quantity can be negative to reduce stock)
        $this->products[$id]['stock'] += $request->quantity;

        if ($this->products[$id]['stock'] < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock',
                'correlation_id' => $correlationId,
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'data' => $this->products[$id],
            'correlation_id' => $correlationId,
        ], 200);
    }
}

