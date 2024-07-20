<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Release;
use App\Traits\ApiResponse;
use App\Repositories\ReleaseRepository;
use App\Http\Requests\StoreReleaseRequest;
use App\Http\Requests\UpdateReleaseRequest;

class ReleaseController extends Controller
{
    use ApiResponse;

    public function __construct(private ReleaseRepository $repository)
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReleaseRequest $request)
    {
        try {
            /** @var User $user */
            $user = auth()->user();

            /** @var Release $release */
            $release = $this->repository->create($request->all()  + ['user_id' => $user->id]);

            return $this->success($release, __('Item crceated successfuly'));
        } catch (Exception $e) {
            return $this->error(__('An error occurred while processing the action'), ['error' => $e->getMessage()], $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Release $release)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReleaseRequest $request, Release $release)
    {
        try {
            /** @var Release $release */
            $release = $release->update($request->all());

            return $this->success($release, __('Item updated successfuly'));
        } catch (Exception $e) {
            return $this->error(__('An error occurred while processing the action'), ['error' => $e->getMessage()], $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Release $release)
    {
        try {
            /** @var Release $release */
            $release = $release->delete();

            return $this->success([], __('Item removed successfuly'));
        } catch (Exception $e) {
            return $this->error(__('An error occurred while processing the action'), ['error' => $e->getMessage()], $e);
        }
    }
}
