<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // getでtasks/にアクセスされた場合の「一覧表示処理」
    public function index()
    {
        if (\Auth::check()) {
            $user = \Auth::user();
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);
            
            return view('tasks.index', [
                'user' => $user,
                'tasks' => $tasks,
            ]);
            
        }else{
            return view('welcome');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     // getでtasks/createにアクセスされた場合の「新規登録画面表示処理」
    public function create()
    {
         $tasks = new Task;

        return view('tasks.create', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     // postでtasks/にアクセスされた場合の「新規登録処理」
    public function store(Request $request)
    {
         $this->validate($request, [
            'status' => 'required|max:10',
            'content' => 'required',
             ]);
        
        $request->user()->tasks()->create([
            'content' => $request->content,
            'status' => $request->status,
        ]);

        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // getでtasks/idにアクセスされた場合の「取得表示処理」
    public function show($id)
    {   
        $tasks = Task::find($id);
       
        if (\Auth::id() !== $tasks->user_id) {
           return redirect('/'); 
         }
          return view('tasks.show', [
            'tasks' => $tasks,
        ]);
         
         

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     // getでtasks/id/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {
         $tasks = Task::find($id);
         
         if (\Auth::id() !== $tasks->user_id) {
           return redirect('/'); 
         }
        return view('tasks.edit', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // putまたはpatchでtasks/idにアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {    
        if (\Auth::id() !== $tasks->user_id) {
           return redirect('/'); 
         }
        $this->validate($request, [
            'status' => 'required|max:10', 
            'content' => 'required',
        ]);

        $tasks = Task::find($id);
        $tasks->status = $request->status; 
        $tasks->content = $request->content;
        $tasks->save();

        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // deleteでtasks/idにアクセスされた場合の「削除処理」
    public function destroy($id)
    {
         $tasks = Task::find($id);
         
        if (\Auth::id() !== $tasks->user_id) {
          return redirect('/');
         }
           $tasks->delete();
           return redirect('/');
    }
}
