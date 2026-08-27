<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Http\Resources\PackageResource;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Package::class);

        $user = request()->user();

        $packages = Package::query()
            ->with('customer')
            ->when($user->isClient(), fn ($q) => $q->where('customer_id', $user->customer?->id))
            ->latest()
            ->paginate(15);

        return PackageResource::collection($packages);
    }

    public function store(StorePackageRequest $request): PackageResource
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('packages', 'public');
        }
        unset($data['photo']);

        return new PackageResource(Package::create($data)->load('customer'));
    }

    public function show(Package $package): PackageResource
    {
        $this->authorize('view', $package);

        return new PackageResource($package->load(['customer', 'purchaseRequest', 'shipments', 'statusHistory.user']));
    }

    public function update(UpdatePackageRequest $request, Package $package): PackageResource
    {
        $this->authorize('update', $package);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('packages', 'public');
            if ($package->photo_path) {
                Storage::disk('public')->delete($package->photo_path);
            }
        }
        unset($data['photo']);

        $package->update($data);

        return new PackageResource($package->load('customer'));
    }

    public function destroy(Package $package): JsonResponse
    {
        $this->authorize('delete', $package);
        $package->delete();

        return response()->json(['message' => 'OK']);
    }
}
