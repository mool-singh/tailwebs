<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->isXmlHttpRequest() || $request->ajax()) {
           
            $query = Student::where('user_id',auth()->id());

            // Search functionality
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('subject', 'like', '%' . $request->search . '%');
            }
    
            // Cursor pagination
            $students = $query->cursorPaginate(10);
    
            return response()->json($students);

        }

        return view('students.index');
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'marks' => 'required|integer',
        ]);

        $student = Student::updateOrCreate(
            ['name' => $request->name, 'subject' => $request->subject,'user_id' => auth()->id()],
            ['marks' => $request->marks]
        );

        return response()->json(['message' => 'Student added successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::where('user_id',auth()->id())->findOrFail($id);
        return response()->json($student);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {

        if ($student->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'marks' => 'required|integer',
        ]);


        $student->update($validated);

        return response()->json(['message' => 'Student updated successfully']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        if ($student->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
    
        $student->delete();
    
        return response()->json(['message' => 'Student deleted successfully']);
    }
}
