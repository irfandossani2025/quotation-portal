@extends('layout')
@section('content')
<div class="page-head"><div><div class="eyebrow">Administration</div><h1>Team access</h1><p>Each preparer's contact details automatically appear on their quotations.</p></div></div>
<div class="grid-2"><section class="panel"><div class="panel-head"><h2>Current users</h2></div><div class="table-wrap"><table><thead><tr><th>Name</th><th>Office</th><th>Role</th></tr></thead><tbody>@foreach($users as $user)<tr><td><strong>{{ $user->name }}</strong><br><small>{{ $user->email }}<br>{{ $user->phone }}</small></td><td>{{ $user->office }}</td><td><span class="badge">{{ $user->role }}</span></td></tr>@endforeach</tbody></table></div></section>
<section class="panel form-panel"><h2 style="margin-top:0">Add a user</h2><form method="post" action="{{ route('users.store') }}">@csrf
<label class="field"><span>Full name</span><input name="name" required></label><label class="field"><span>Email</span><input type="email" name="email" required></label><label class="field"><span>Phone number</span><input name="phone" required></label>
<div class="grid-2"><label class="field"><span>Office</span><select name="office"><option>Muscat</option><option>Dubai</option></select></label><label class="field"><span>Role</span><select name="role"><option value="preparer">Quotation preparer</option><option value="pricing">Dubai pricing</option><option value="admin">Administrator</option></select></label></div>
<label class="field"><span>Temporary password (12+ characters)</span><input type="password" name="password" minlength="12" required></label><button class="btn green full">Create user</button></form></section></div>
@endsection
