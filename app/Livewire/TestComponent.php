<?php

namespace App\Livewire;

use Livewire\Component;

class TestComponent extends Component
{
    public $count = 0;
    public $message = 'DDT by Logix - Livewire is working!';

    public function increment()
    {
        $this->count++;
    }

    public function decrement()
    {
        $this->count--;
    }

    public function render()
    {
        return view('livewire.test-component');
    }
}
