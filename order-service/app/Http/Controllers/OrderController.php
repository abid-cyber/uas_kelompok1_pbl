<?php

namespace App\Http\Controllers;

use App\Http\Services\ProductServiceClient;
use App\Http\Services\UserServiceClient;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected $userServiceClient;
    protected $productServiceClient;

    public function __construct(
        UserServiceClient $userServiceClient,
        ProductServiceClient $productServiceClient
    ) {
        $this->userServiceClient = $userServiceClient;
        $this->productServiceClient = $productServiceClient;
    }

    /**
     * Create a new order
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $correlationId = $request->header('X-Correlation-ID');
        $token = $request->bearerToken();

        // Validate request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'correlation_id' => $correlationId,
            ], 422);
        }

        try {
            DB::beginTransaction();

            // 1. Validate token dengan User Service
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authorization token required',
                    'correlation_id' => $correlationId,
                ], 401);
            }

            $userProfile = $this->userServiceClient->validateToken($token, $correlationId);
            
            // 2. Validate user_id dengan User Service
            $userData = $this->userServiceClient->getUserById($request->user_id, $token, $correlationId);
            
            // 3. Validate products dan check stock dengan Product Service
            foreach ($request->items as $item) {
                $product = $this->productServiceClient->getProductById($item['product_id'], $correlationId);
                
                // Check stock availability
                if (!$this->productServiceClient->checkStock($item['product_id'], $item['quantity'], $correlationId)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product ID: {$item['product_id']}",
                        'correlation_id' => $correlationId,
                    ], 400);
                }
            }

            // 4. Create order
            $order = Order::create([
                'user_id' => $request->user_id,
                'items' => $request->items,
                'total' => $request->total,
                'status' => 'pending',
            ]);

            // 5. Update stock di Product Service
            foreach ($request->items as $item) {
                // Update stock (reduce by quantity)
                $this->productServiceClient->updateStock($item['product_id'], -$item['quantity'], $correlationId);
            }

            // Update order status to completed
            $order->update(['status' => 'completed']);

            DB::commit();

            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'user_id' => $request->user_id,
                'correlation_id' => $correlationId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order,
                'correlation_id' => $correlationId,
            ], 201);

        } catch (\App\Exceptions\ServiceUnavailableException $e) {
            DB::rollBack();
            return $e->render($request);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'correlation_id' => $correlationId,
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'Failed to create order',
                'correlation_id' => $correlationId,
            ], 500);
        }
    }

    /**
     * Get all orders
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $correlationId = $request->header('X-Correlation-ID');
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization token required',
                'correlation_id' => $correlationId,
            ], 401);
        }

        try {
            // Validate token
            $this->userServiceClient->validateToken($token, $correlationId);

            $orders = Order::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Orders retrieved successfully',
                'data' => $orders,
                'correlation_id' => $correlationId,
            ], 200);
        } catch (\App\Exceptions\ServiceUnavailableException $e) {
            return $e->render($request);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve orders', [
                'error' => $e->getMessage(),
                'correlation_id' => $correlationId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders',
                'correlation_id' => $correlationId,
            ], 500);
        }
    }

    /**
     * Get order by ID
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $correlationId = $request->header('X-Correlation-ID');
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization token required',
                'correlation_id' => $correlationId,
            ], 401);
        }

        try {
            // Validate token
            $this->userServiceClient->validateToken($token, $correlationId);

            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                    'correlation_id' => $correlationId,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order retrieved successfully',
                'data' => $order,
                'correlation_id' => $correlationId,
            ], 200);
        } catch (\App\Exceptions\ServiceUnavailableException $e) {
            return $e->render($request);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve order', [
                'error' => $e->getMessage(),
                'order_id' => $id,
                'correlation_id' => $correlationId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve order',
                'correlation_id' => $correlationId,
            ], 500);
        }
    }
}

