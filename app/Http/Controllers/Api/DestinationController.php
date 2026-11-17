<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexDestinationRequest;
use App\Http\Resources\DestinationResource;
use App\Models\Destination;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DestinationController extends Controller
{
    public function index(IndexDestinationRequest $request): AnonymousResourceCollection
    {
        $destinations = Destination::query()
            ->search($request->search())
            ->sortedBy($request->sort(), $request->direction())
            ->paginate($request->perPage())
            ->withQueryString();

        return DestinationResource::collection($destinations);
    }
}
