<?php

namespace App\Http\Controllers;

use App\YoutubeVideo;
use Illuminate\Http\Request;
use App\Chan;

class YoutubeVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Chan $chan)
    {
		$youtubeVideos = YoutubeVideo::where('chan_name', $chan->name)->orderBy('created_at', 'DESC')->paginate(20);
		return view('youtubeVideos.index', compact('chan', 'youtubeVideos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\YoutubeVideos  $youtubeVideos
     * @return \Illuminate\Http\Response
     */
    public function show(YoutubeVideo $youtubeVideo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\YoutubeVideos  $youtubeVideos
     * @return \Illuminate\Http\Response
     */
    public function edit(YoutubeVideo $youtubeVideo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\YoutubeVideos  $youtubeVideos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, YoutubeVideo $youtubeVideo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\YoutubeVideos  $youtubeVideos
     * @return \Illuminate\Http\Response
     */
    public function destroy(YoutubeVideo $youtubeVideo)
    {
        //
    }
}
