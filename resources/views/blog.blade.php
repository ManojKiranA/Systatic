@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="content p-6 leading-normal tracking-normal">
        <h1>@title()</h1>
        @content()
        <ul>
        	<li>Posts list coming soon</li>
        </ul>
    </div>
</div>
@endsection