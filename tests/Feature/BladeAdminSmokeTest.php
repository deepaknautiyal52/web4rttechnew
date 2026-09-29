<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Employee;
use App\Models\FinanceEntry;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Renewal;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BladeAdminSmokeTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::create(['name' => 'Smoke Admin', 'email' => 'smoke'.uniqid().'@example.com', 'password' => bcrypt('password123'), 'role' => 'admin']);
    }

    public function test_login_flow()
    {
        $u = $this->admin();
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->post('/admin/login', ['email' => $u->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/admin/login', ['email' => $u->email, 'password' => 'password123'])->assertRedirect('/admin');
        $this->get('/admin')->assertOk()->assertSee('Welcome back, Smoke Admin');
    }

    public function test_every_admin_page_renders_with_data()
    {
        $u = $this->admin();
        $client = Client::create(['name' => 'Priya', 'company' => 'Acme', 'status' => 'active', 'email' => 'p@acme.test']);
        $emp = Employee::create(['name' => 'Dev One', 'currency' => 'INR', 'status' => 'active', 'monthly_salary' => 48000]);
        $proj = Project::create(['client_id' => $client->id, 'name' => 'Acme site', 'status' => 'in_progress', 'currency' => 'INR', 'budget' => 1000, 'deadline' => now()->subDay(), 'employee_id' => $emp->id]);
        $inv = Invoice::create(['number' => 'INV-T-1', 'client_id' => $client->id, 'project_id' => $proj->id, 'status' => 'sent', 'currency' => 'INR', 'items' => [['description' => 'Site', 'quantity' => 1, 'unit_price' => 5000]], 'tax_rate' => 18, 'issue_date' => now()->subDays(30), 'due_date' => now()->subDays(5)]);
        $q = Quotation::create(['number' => 'QT-T-1', 'client_id' => $client->id, 'title' => 'Quote', 'status' => 'sent', 'currency' => 'INR', 'items' => [['description' => 'X', 'quantity' => 2, 'unit_price' => 100]], 'tax_rate' => 18, 'issue_date' => now()]);
        Renewal::create(['client_id' => $client->id, 'type' => 'domain', 'name' => 'acme.com', 'currency' => 'INR', 'billing_cycle' => 'yearly', 'expiry_date' => now()->addDays(5)]);
        Ticket::create(['client_id' => $client->id, 'title' => 'Bug', 'priority' => 'urgent', 'status' => 'open']);
        FinanceEntry::create(['type' => 'expense', 'category' => 'hosting', 'title' => 'VPS', 'amount' => 900, 'currency' => 'INR', 'period_start' => now(), 'recurrence' => 'monthly']);
        Contact::create(['name' => 'Lead A', 'email' => 'a@x.test', 'subject' => 'Need site', 'message' => 'Hi', 'status' => 'won', 'deal_value' => 500, 'won_at' => now(), 'service_interested' => 'seo']);
        Contact::create(['name' => 'Lead B', 'email' => 'b@x.test', 'subject' => 'Ads', 'message' => 'Hi', 'status' => 'lost', 'lost_reason' => 'No budget', 'next_follow_up_at' => now()->subDay()]);

        $this->actingAs($u);
        $pages = [
            '/admin', '/admin/submissions', '/admin/submissions?view=follow_up', '/admin/submissions?status=won&search=Lead',
            '/admin/quotations', '/admin/quotations?create=1', "/admin/quotations?edit={$q->id}",
            '/admin/clients', '/admin/clients?create=1', "/admin/clients?edit={$client->id}", "/admin/clients/{$client->id}",
            '/admin/projects', "/admin/projects?edit={$proj->id}", '/admin/projects?active=1',
            '/admin/timesheets', '/admin/timesheets?create=1&prefill[project_id]='.$proj->id, '/admin/timesheets?days=7',
            '/admin/tickets', '/admin/tickets?status=', '/admin/tickets?create=1',
            '/admin/invoices', '/admin/invoices?create=1', "/admin/invoices?edit={$inv->id}", '/admin/invoices?status=overdue', "/admin/invoices?open={$inv->id}",
            '/admin/finances', '/admin/finances?create=1',
            '/admin/renewals', '/admin/renewals?due_within=7', '/admin/renewals?create=1',
            '/admin/team', '/admin/team?create=1', "/admin/team?edit={$emp->id}",
            '/admin/news', '/admin/news?create=1', '/admin/news?is_published=0',
            '/admin/users', '/admin/users?create=1', "/admin/users?edit={$u->id}",
            '/admin/audit-log', '/admin/audit-log?action=created',
            '/admin/settings',
            "/admin-print/invoice/{$inv->id}", "/admin-print/quotation/{$q->id}",
        ];
        foreach ($pages as $p) {
            $res = $this->get($p);
            $this->assertSame(200, $res->status(), "GET $p returned ".$res->status()."\n".substr(strip_tags($res->getContent()), 0, 800));
        }
        $this->get('/admin/export/invoices')->assertOk();
        $this->get('/admin')->assertSee('Projects at risk')->assertSee('Overdue invoices')->assertSee('Why deals are lost');
        $this->get('/admin/invoices')->assertSee('INV-T-1')->assertSee('₹5,900');
    }

    public function test_crud_flows()
    {
        $u = $this->admin();
        $this->actingAs($u);

        // Client create with validation error, then success.
        $this->from('/admin/clients?create=1')->post('/admin/clients', ['_form' => 'resource', 'name' => '', 'status' => 'active'])
            ->assertRedirect('/admin/clients?create=1')->assertSessionHasErrors('name');
        $this->post('/admin/clients', ['_form' => 'resource', '_back' => url('/admin/clients?create=1&search=x'), 'name' => 'New Co', 'status' => 'active'])
            ->assertRedirect(url('/admin/clients?search=x'))->assertSessionHas('notice', 'Client created successfully.');
        $client = Client::where('name', 'New Co')->firstOrFail();

        // Invoice with line items, then payment, then remove payment.
        $this->post('/admin/invoices', ['client_id' => $client->id, 'status' => 'draft', 'currency' => 'INR', 'issue_date' => today()->toDateString(), 'tax_rate' => 18,
            'items' => [['description' => 'Web', 'quantity' => 2, 'unit_price' => 1000]]])->assertSessionHasNoErrors();
        $inv = Invoice::where('client_id', $client->id)->firstOrFail();
        $this->assertEquals(2360, (float) $inv->total);
        $this->post("/admin/invoices/{$inv->id}/payments", ['amount' => 99999, 'paid_on' => today()->toDateString()])->assertSessionHasErrors('amount');
        $this->post("/admin/invoices/{$inv->id}/payments", ['_back' => url('/admin/invoices'), 'amount' => 1000, 'paid_on' => today()->toDateString(), 'method' => 'UPI'])
            ->assertRedirect(url('/admin/invoices?open='.$inv->id));
        $this->assertSame('partially_paid', $inv->fresh()->status);
        $payment = $inv->payments()->first();
        $this->delete("/admin/payments/{$payment->id}", ['_back' => url('/admin/invoices')])->assertRedirect();
        $this->assertSame('sent', $inv->fresh()->status);

        // Quotation -> invoice.
        $this->post('/admin/quotations', ['title' => 'Q', 'client_id' => $client->id, 'status' => 'draft', 'currency' => 'USD', 'issue_date' => today()->toDateString(), 'tax_rate' => 0,
            'items' => [['description' => 'A', 'quantity' => 1, 'unit_price' => 500]]])->assertSessionHasNoErrors();
        $q = Quotation::where('title', 'Q')->firstOrFail();
        $this->post("/admin/quotations/{$q->id}/invoice")->assertRedirect(route('admin.invoices.index'));
        $this->assertSame('accepted', $q->fresh()->status);

        // Lead status + activity + convert.
        $lead = Contact::create(['name' => 'L', 'email' => 'l@x.test', 'subject' => 'S', 'message' => 'M', 'status' => 'new']);
        $this->post("/admin/submissions/{$lead->id}/activities", ['_back' => url('/admin/submissions'), 'type' => 'call', 'body' => 'Called'])->assertRedirect(url('/admin/submissions?open='.$lead->id));
        $this->assertSame('contacted', $lead->fresh()->status);
        $this->patch("/admin/submissions/{$lead->id}", ['status' => 'won', 'deal_value' => '750', 'next_follow_up_at' => '2030-01-01'])->assertRedirect();
        $this->assertSame('won', $lead->fresh()->status);
        $this->assertNull($lead->fresh()->next_follow_up_at);
        $this->post("/admin/submissions/{$lead->id}/convert")->assertRedirect();
        $this->assertNotNull($lead->fresh()->client_id);

        // Finance entry, renewal renew, article checkbox, user protections.
        $this->post('/admin/finances', ['type' => 'expense', 'category' => 'domain', 'title' => 'D', 'amount' => 800, 'currency' => 'INR', 'period_start' => today()->toDateString(), 'recurrence' => ''])->assertSessionHasNoErrors();
        $r = Renewal::create(['type' => 'ssl', 'name' => 'r', 'currency' => 'INR', 'billing_cycle' => 'monthly', 'expiry_date' => '2026-01-31']);
        $this->post("/admin/renewals/{$r->id}/renew", ['_back' => url('/admin/renewals')])->assertSessionHas('notice');
        $this->assertSame('2026-02-28', $r->fresh()->expiry_date->toDateString());
        $this->post('/admin/news', ['title' => 'Hello World', 'body' => 'Text', 'is_published' => '0', 'published_at' => ''])->assertSessionHasNoErrors();
        $this->assertFalse((bool) \App\Models\Article::where('slug', 'hello-world')->firstOrFail()->is_published);
        $this->delete("/admin/users/{$u->id}", ['_back' => url('/admin/users')])->assertSessionHasErrors('user');
        $this->put("/admin/users/{$u->id}", ['name' => 'Renamed', 'email' => $u->email, 'role' => 'admin', 'password' => ''])->assertSessionHasNoErrors();
        $this->assertSame('Renamed', $u->fresh()->name);
    }

    public function test_roles_and_public_contact()
    {
        $dev = User::create(['name' => 'Dev', 'email' => 'dev'.uniqid().'@example.com', 'password' => bcrypt('x12345678'), 'role' => 'developer']);
        $this->actingAs($dev)->get('/admin/invoices')->assertStatus(403)->assertSee('Your role does not have access');
        $this->actingAs($dev)->get('/admin/projects')->assertOk();
        $this->actingAs($dev)->get('/admin')->assertOk()->assertDontSee('Inquiries');

        auth()->logout();
        $this->post('/contact', ['name' => '', 'email' => 'bad'])->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
        $this->post('/contact', ['name' => 'Vis', 'email' => 'v@x.test', 'subject' => 'Hi', 'message' => 'Hello', 'service_interested' => 'seo'])
            ->assertRedirect(route('contact'))->assertSessionHas('contact_success');
        $this->assertDatabaseHas('contacts', ['email' => 'v@x.test', 'service_interested' => 'seo']);
    }
}
