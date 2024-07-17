<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Bank;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Repositories\BankRepository;
use App\Http\Requests\StoreBankRequest;
use App\Http\Requests\UpdateBankRequest;

class BankController extends Controller
{
    use ApiResponse;

    public function __construct(private BankRepository $repository)
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->success($this->repository->all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBankRequest $request)
    {
        try {
            /** @var User $user */
            $user = auth()->user();

            /** @var Bank $bank */
            $bank = $this->repository->create($request->all());

            // attach user bank
            $user->banks()->attach($bank->id);

            return $this->success($bank, __('Item crceated successfuly'));
        } catch (Exception $e) {
            return $this->error(__('An error occurred while processing the action'), 500, ['error' => $e->getMessage()], $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank)
    {
        $item  = $this->repository->find($bank->id);

        if (!$item)
            return $this->error(__('Record not found'));

        return $this->success($item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBankRequest $request, Bank $bank)
    {
        try {
            /** @var Bank $bank */
            $this->repository->update($bank->id, $request->all());

            return $this->success($this->repository->find($bank->id), __('Item updated successfuly'));
        } catch (Exception $e) {
            return $this->error(__('An error occurred while processing the action'), 500, ['error' => $e->getMessage()], $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        try {
            $this->repository->delete($bank->id);
            return $this->success([__('Item removed successfuly')]);
        } catch (Exception $e) {
            return $this->error(__('An error occurred while processing the action'), 500, ['error' => $e->getMessage()], $e);
        }
    }
}
