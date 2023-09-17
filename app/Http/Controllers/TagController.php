<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Client\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = DB::table('tags')
        ->select('tags.*', 'tag_user.user_id')
        ->leftJoin('tag_user', function (JoinClause $join) {
            $join->on('tags.id', '=', 'tag_user.tag_id')
            ->where('tag_user.user_id', '=', auth()->user()->id);
        })
        ->paginate(10);
        return view('tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() : View
    {
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request): RedirectResponse
    {
        Tag::create($request->validated());
        Session::flash('message', 'Tag creado con éxito');
 
        return to_route('tags.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function subscribe(int $id): RedirectResponse
    {
        $user = User::find(auth()->user()->id);
        $tag = Tag::find($id);
        $user->tags()->attach($tag);
        Session::flash('message', 'Subscripción realizada con éxito');
        return to_route('tags.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function unsubscribe(int $id): RedirectResponse
    {
        $user = User::find(auth()->user()->id);
        $tag = Tag::find($id);
        $user->tags()->detach($tag);
        Session::flash('message', 'Desubscripción realizada con éxito');
        return to_route('tags.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $tag = Tag::find($id);
        return view('tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());
        Session::flash('message', 'Tag editado con éxito');
 
        return to_route('tags.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $tag = Tag::find($id);
        $tag->users()->detach();
        $tag->delete();
        Session::flash('message', 'Tag eliminado con éxito');
 
        return to_route('tags.index');
    }
}
