@extends('layouts.app')
@section('content')
<div class="box content">

</div>
@endsection

@section('scripts')
<script src='https://cloud.tinymce.com/5/tinymce.min.js?apiKey="{{env('TINYMCE_KEY', 'TINYMCE_KEY_NOT_SET')}}"'></script>
<script src="{{ mix('js/tinyMCE.js') }}"></script>
@endsection
