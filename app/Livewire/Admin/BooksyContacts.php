<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\BooksyProspect;
use App\Models\Contact;

class BooksyContacts extends Component
{
    public $contacts;
    public $showAddForm = false;
    public $showEditForm = false;
    public $newContact = [];
    public $editingContact = [];
    public $editIndex = null;

    public function cancelAddForm()
    {
        $this->showAddForm = false;
        $this->newContact = [];
    }

    public function cancelEditForm()
    {
        $this->showEditForm = false;
        $this->editingContact = [];
        $this->editIndex = null;
    }

    public function mount()
    {
        $this->contacts = BooksyProspect::all();
    }

    public function render()
    {
        $this->contacts = BooksyProspect::all();
        return view('livewire.admin.booksy-contacts');
    }

    public function toggleAddForm()
    {
        $this->showAddForm = !$this->showAddForm;
        $this->newContact = [];
    }

    public function addContact()
    {
        $this->validate([
            'newContact.owner_name' => 'required',
            'newContact.salon_name' => 'required',
        ]);
        BooksyProspect::create($this->newContact);
        $this->showAddForm = false;
        session()->flash('message', 'Contato adicionado com sucesso!');
    }

    public function editContact($index)
    {
        $contact = $this->contacts[$index];
        $this->editingContact = $contact->toArray();
        $this->editIndex = $contact->id;
        $this->showEditForm = true;
    }

    public function updateContact()
    {
        $this->validate([
            'editingContact.owner_name' => 'required',
            'editingContact.salon_name' => 'required',
        ]);
        $contact = BooksyProspect::find($this->editIndex);
        $contact->update($this->editingContact);
        $this->showEditForm = false;
        session()->flash('message', 'Contato atualizado com sucesso!');
    }

    public function deleteContact($index)
    {
        $contact = $this->contacts[$index];
        $contact->delete();
        session()->flash('message', 'Contato removido com sucesso!');
    }

    public function convertToClient($index)
    {
        $contact = $this->contacts[$index];
        // Transfere para tabela contacts
        Contact::create([
            'owner_name' => $contact->owner_name,
            'tenant_name' => $contact->salon_name,
            'tipo' => $contact->salon_type,
            'phone' => $contact->phone,
            'address' => $contact->address,
            'notas' => $contact->notes,
            'employee_count' => $contact->employee_count,
        ]);
        $contact->converted = true;
        $contact->save();
        session()->flash('message', 'Contato convertido em cliente!');
    }
}