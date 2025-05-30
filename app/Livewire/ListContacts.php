<?php

namespace App\Livewire;
use App\Models\Contact;
use Livewire\Component;


class ListContacts extends Component
{
    public $editedContactIndex = null;
    public $editedContactField = null;
    public $contacts = [];
   

    protected $rules = [
        'contacts.*.name' => ['required'],
        'contacts.*.email' => ['required'],
    ];

    protected $validationAttributes = [
        'contacts.*.name' => 'name',
        'contacts.*.email' => 'email',
    ];
    
    public function mount()
    {
        $this->contacts = Contact::all()->toArray();
    }


    public function render()
    {
        //$this->contacts = Contact::all()->toArray();
        
        return view('livewire.list-contacts', ['contacts'=>$this->contacts]);
    }
    public function editContact($contactIndex)
    {
        $this->editedContactIndex = $contactIndex;
    }
    
    public function editContactField($contactIndex, $fieldName)
    {
        $this->editedContactField = $contactIndex.'.'.$fieldName;
    }
    public function saveContact($contactIndex)
    {  
        $this->validate();
        $contact = $this->contacts[$contactIndex] ?? null;

        if (!is_null($contact)) {            
            $contacto = Contact::find($contact['id']);            
            $contacto->name = $contact['name'];
            $contacto->email = $contact['email'];
            $contacto->phone = $contact['phone'];
            $contacto->status = $contact['status'];
            $contacto->address = $contact['address'];
            $contacto->complement = $contact['complement'];
            $contacto->city = $contact['city'];
            $contacto->state = $contact['state'];
            $contacto->cep = $contact['cep'];
            $contacto->salon = $contact['salon'];
            $contacto->save();
        }
        
        $this->editedContactIndex = null;
        $this->editedContactField = null;

    }
    public function deleteContact($contactIndex)
    {
        $contact = $this->contacts[$contactIndex] ?? null;
        $contacto = Contact::find($contact['id']);
        $contacto->delete();

    }
    
}
