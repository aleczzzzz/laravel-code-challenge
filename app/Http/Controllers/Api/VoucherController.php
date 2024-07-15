<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Http\Requests\StoreVoucherRequest;
use App\Http\Requests\UpdateVoucherRequest;
use App\Http\Resources\VoucherCollection;
use App\Http\Resources\VoucherResource;
use App\Services\VoucherService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class VoucherController extends Controller
{

    public function __construct(private VoucherService $voucherService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new VoucherCollection(
            auth()
                ->user()
                ->vouchers
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        try {
            $this->voucherService
                ->createVoucherForUser(
                    auth()->user()
                );
        } catch (\Throwable $th) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $th->getMessage()
            ]);
        }

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Successfully created voucher.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Voucher $voucher)
    {
        Gate::authorize('view', $voucher);

        return new VoucherResource($voucher);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Voucher $voucher)
    {
        Gate::authorize('delete', $voucher);

        try {
            $voucher->delete();
        } catch (\Throwable $th) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to delete voucher.'
            ]);
        }

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Successfully deleted voucher.'
        ]);
    }
}
