<?php

namespace App\Livewire;
use App\Models\Contact;
use Livewire\Component;


class ListContacts extends Component
{
    public $editedContactIndex = null;
    public $editedContactField = null;
    public $contacts = [];
    public $showAddForm = false;
    public $showEditForm = false;
    public $editingContact = [
        'id' => null,
        'owner_name' => '',
        'email' => '',
        'phone' => '',
        'tipo' => '',
        'tenant_name' => '',
        'address' => '',
        'neighborhood' => '',
        'city' => '',
        'state' => '',
        'notas' => '',
    ];
    public $newContact = [
        'owner_name' => '',
        'email' => '',
        'phone' => '',
        'tipo' => '',
        'tenant_name' => '',
        'address' => '',
        'neighborhood' => '',
        'city' => '',
        'state' => '',
        'notas' => '',
    ];
   

    protected $rules = [
        'contacts.*.owner_name' => ['required'],
        'contacts.*.email' => ['required', 'email'],
        'newContact.owner_name' => ['required'],
        'newContact.email' => ['required', 'email'],
        'editingContact.owner_name' => ['required'],
        'editingContact.email' => ['required', 'email'],
    ];

    protected $validationAttributes = [
        'contacts.*.owner_name' => 'name',
        'contacts.*.email' => 'email',
        'newContact.owner_name' => 'name',
        'newContact.email' => 'email',
        'editingContact.owner_name' => 'name',
        'editingContact.email' => 'email',
    ];
    
    public function mount()
    {
        $this->loadContacts();
    }

    public function loadContacts()
    {
        $this->contacts = Contact::with('pagbypayment')->get()->map(function($contact) {
            $contactArray = $contact->toArray();
            $contactArray['has_paid'] = $contact->pagbypayment()->where('status', 'RECEIVED')->exists();
            $contactArray['payment_count'] = $contact->pagbypayment()->count();
            $contactArray['total_paid_amount'] = $contact->pagbypayment()->where('status', 'RECEIVED')->sum('amount');
            $contactArray['last_payment_plan'] = $contact->pagbypayment()->latest()->first()?->plan;

            $data = $contact->pagbypayment()->latest()->first()->created_at;
                $timestamp = strtotime($data);
                $data_pgto = date('d/m/Y', $timestamp);

            if($contactArray['last_payment_plan'] == 'mensal'){
                $contactArray['last_payment_plan'] = 'Mensal';
                $data_vct = date('d/m/Y', strtotime($data . ' +1 month'));
            } elseif($contactArray['last_payment_plan'] == 'trimestral'){
                $contactArray['last_payment_plan'] = 'Trimestral';
                $data_vct = date('d/m/Y', strtotime($data . ' +3 months'));
            } elseif($contactArray['last_payment_plan'] == 'semestral'){
                $contactArray['last_payment_plan'] = 'Semestral';
                $data_vct = date('d/m/Y', strtotime($data . ' +6 months'));
            } elseif($contactArray['last_payment_plan'] == 'anual'){
                $contactArray['last_payment_plan'] = 'Anual';
                $data_vct = date('d/m/Y', strtotime($data . ' +12 months'));
            } else{
                $data_vct = 'N/A';
            }
            $contactArray['due_date'] = $data_vct;   
            
            $contactArray['last_payment_date'] = $data_pgto;

            $contactArray['last_payment_amount'] = $contact->pagbypayment()->latest()->first()?->amount;
            $contactArray['last_payment_method'] = $contact->pagbypayment()->latest()->first()?->payment_method;
            
            $contactArray['last_payment_external_id'] = $contact->pagbypayment()->latest()->first()?->external_id;
            $contactArray['last_payment_id'] = $contact->pagbypayment()->latest()->first()?->id;
            
            $contactArray['last_payment_employee_count'] = $contact->pagbypayment()->latest()->first()?->employee_count;
            
            $contactArray['last_payment_asaas_payment_id'] = $contact->pagbypayment()->latest()->first()?->asaas_payment_id;

            $contactArray['last_payment_status'] = $contact->pagbypayment()->latest()->first()?->status;

            return $contactArray;
        })->toArray();
    }


    public function render()
    {
        return view('livewire.list-contacts');
    }
    public function editContact($contactIndex)
    {
        $contact = $this->contacts[$contactIndex];
        $this->editingContact = [
            'id' => $contact['id'],
            'owner_name' => $contact['owner_name'] ?? $contact['name'] ?? '',
            'email' => $contact['email'],
            'phone' => $contact['phone'],
            'tipo' => $contact['tipo'],
            'tenant_name' => $contact['tenant_name'],
            'address' => $contact['address'],
            'neighborhood' => $contact['neighborhood'],
            'city' => $contact['city'],
            'state' => $contact['state'],
            'notas' => $contact['notas'] ?? '',
        ];
        $this->showEditForm = true;
        $this->showAddForm = false; // Garante que apenas um formulário seja exibido
    }

    public function deleteContact($contactIndex)
    {
        $contact = $this->contacts[$contactIndex] ?? null;
        if ($contact) {
            Contact::destroy($contact['id']);
            $this->loadContacts();
            session()->flash('message', 'Contato excluído com sucesso!');
        }
    }

    public function toggleAddForm()
    {
        $this->showAddForm = true;
        $this->showEditForm = false; // Garante que apenas um formulário seja exibido
        $this->resetNewContact();
    }

    public function cancelAddForm()
    {
        $this->showAddForm = false;
    }

    public function resetNewContact()
    {
        $this->newContact = [
            'owner_name' => '',
            'email' => '',
            'phone' => '',
            'tipo' => '',
            'tenant_name' => '',
            'address' => '',
            'neighborhood' => '',
            'city' => '',
            'state' => '',
        ];
    }

    public function addContact()
    {
        $this->validate([
            'newContact.owner_name' => 'required|string|max:255',
            'newContact.email' => 'required|email|max:255',
            'newContact.phone' => 'required|string|max:20',
            'newContact.tipo' => 'required|string|max:50',
            'newContact.tenant_name' => 'required|string|max:255',
            'newContact.address' => 'required|string|max:255',
            'newContact.neighborhood' => 'required|string|max:255',
            'newContact.city' => 'required|string|max:255',
            'newContact.state' => 'required|string|max:255',
            'newContact.notas' => 'nullable|string|max:1000',
        ]);

        Contact::create($this->newContact);
        
        $this->loadContacts();
        $this->showAddForm = false;
        $this->resetNewContact();
        
        session()->flash('message', 'Contato adicionado com sucesso!');
    }

    public function updateContact()
    {
        $this->validate([
            'editingContact.owner_name' => 'required|string|max:255',
            'editingContact.email' => 'required|email|max:255',
            'editingContact.phone' => 'required|string|max:20',
            'editingContact.tipo' => 'required|string|max:50',
            'editingContact.tenant_name' => 'required|string|max:255',
            'editingContact.address' => 'required|string|max:255',
            'editingContact.neighborhood' => 'required|string|max:255',
            'editingContact.city' => 'required|string|max:255',
            'editingContact.state' => 'required|string|max:255',
            'editingContact.notas' => 'nullable|string|max:1000',
        ]);

        $contact = Contact::find($this->editingContact['id']);
        $contact->update([
            'owner_name' => $this->editingContact['owner_name'],
            'email' => $this->editingContact['email'],
            'phone' => $this->editingContact['phone'],
            'tipo' => $this->editingContact['tipo'],
            'tenant_name' => $this->editingContact['tenant_name'],
            'address' => $this->editingContact['address'],
            'neighborhood' => $this->editingContact['neighborhood'],
            'city' => $this->editingContact['city'],
            'state' => $this->editingContact['state'],
            'notas' => $this->editingContact['notas'],
        ]);
        
        $this->loadContacts();
        $this->showEditForm = false;
        $this->resetEditingContact();
        
        session()->flash('message', 'Contato atualizado com sucesso!');
    }

    public function cancelEditForm()
    {
        $this->showEditForm = false;
        $this->resetEditingContact();
    }

    public function resetEditingContact()
    {
        $this->editingContact = [
            'id' => null,
            'owner_name' => '',
            'email' => '',
            'phone' => '',
            'tipo' => '',
            'tenant_name' => '',
            'address' => '',
            'neighborhood' => '',
            'city' => '',
            'state' => '',
            'notas' => '',
        ];
    }
    
}
