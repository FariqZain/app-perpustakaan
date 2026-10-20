<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MemberController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return MemberResource::collection(Member::paginate(10));
    }

    public function show(string $id): MemberResource
    {
        return new MemberResource(Member::findOrFail($id));
    }
}
