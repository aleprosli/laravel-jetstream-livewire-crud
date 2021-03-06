<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class Posts extends Component
{
    use WithPagination, WithFileUploads;

    public $title, $description, $post_id, $image;

    public $isOpen = 0;

    public function render()
    {
        return view('livewire.posts', [
            'posts' => Post::paginate(5),
        ]);
    }

    public function create()
    {
        //call reset column
        $this->resetInputFields();

        //call modal
        $this->openModal();
    }

    private function resetInputFields()
    {
        //reset column
        $this->title = '';
        $this->description = '';
        $this->post_id = '';
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    //validation rules
    protected $rules = [
        'title' => 'required|min:6',
        'description' => 'required',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];
    
    public function store()
    {
        //updateorcreate, check id, and column declared on top
        Post::updateOrCreate(
            ['id' => $this->post_id],
            [
            'title' => $this->title,
            'description' => $this->description
            ]
        );

        //return message
        session()->flash('message', 
            $this->post_id ? 'Post Updated Successfully.' : 'Post Created Successfully.');
  
        //call closemodal
        $this->closeModal();

        //call resetform
        $this->resetInputFields();
    }

    public function storeWithValidation()
    {
        $this->validate();

        $validate['image'] = $this->image->store('files', 'public');

        // Execution doesn't reach here if validation fails
        Post::updateOrCreate(
            ['id' => $this->post_id],
            [
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            ]
        );

        //return message
        session()->flash('message', 'Post Created Successfully.');
  
        //call closemodal
        $this->closeModal();

        //call resetform
        $this->resetInputFields();
    }

    public function storeWithImage()
    {
        $post = $this->validate([
            'title' => 'required|min:6',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
  
        $post['image'] = $this->image->store('files', 'public');
  
        Post::create($post);

        //return message
        session()->flash('message', 'Post Created Successfully.');
  
        //call closemodal
        $this->closeModal();

        //call resetform
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $this->post_id = $id;
        $this->title = $post->title;
        $this->body = $post->body;
        $this->image = $post->image;
    
        $this->openModal();
    }

    public function delete($id)
    {
        Post::find($id)->delete();
        session()->flash('message', 'Post Deleted Successfully.');
    }

}
