<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;

class HighscoreController extends Controller
{
  use IngameTrait;

  /**
   * Shows the facilities index page
   *
   * @param  int  $id
   * @return Response
   */
  public function index(Request $request)
  {
    return view('ingame.highscore.index');
  }
}