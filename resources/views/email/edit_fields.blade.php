<!-- Template Name Field -->
 {{-- <script src="https://cloud.tinymce.com/5/tinymce.min.js"></script>
  <script>tinymce.init({selector:'textarea'});</script>
 --}}
<script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script> 
<script type="text/javascript">
//<![CDATA[
        bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
  //]]>
</script>

@php
foreach ($get_template as $key => $value) 
{
  $template_name= $value->template_name;
  // $category= $value->category;
  $activity_description= $value->activity_description;
  $message= $value->message;
  $subject= $value->subject;
  $attachment_path= $value->attachment_path;
}
@endphp

 {!! Form::hidden('id', $id, ['class' => 'form-control']) !!}
<div class="form-group col-sm-12">
    {!! Form::label('template_name', 'Template Name:') !!}
    {!! Form::text('template_name', $template_name, ['class' => 'form-control','required'=>'required', 'placeholder'=>'Enter the Template name For your Reference']) !!}
</div>

{{-- <div class="form-group col-sm-12">
    {!! Form::label('category', 'Activity category:') !!}
    {{'No Of Available Category is: '.count($get_category)}}
    <select name="category" class="form-control">
        @php
          $i=1;
          foreach ($get_category as $key => $value) {
            if ($value->category==$category) { $selected="selected";}
            else{$selected="";}
                   echo '<option  value="'.$value->category.'" '.$selected.'>'.$i.'.'.$value->category.'</option>';
                   $i=$i+1;
          }
        @endphp
    </select>
  
</div> --}}

<div class="form-group col-sm-12">
    {!! Form::label('description', 'Activity Description:') !!}
    {{'No Of Available Description is: '.count($get_description)}}
    <select name="activity_description" class="form-control">
        @php
          $i=1;
          foreach ($get_description as $key1 => $value1) {
              if ($value1->activity_description==$activity_description) { $selected="selected";}
            else{$selected="";}
                   echo '<option  value="'.$value1->activity_description.'" '.$selected.'>'.$i.'.'.$value1->activity_description.'</option>';
                   $i=$i+1;
          }
        @endphp
    </select>
  
</div>

<!-- Subject Field -->
<div class="form-group col-sm-12">
    {!! Form::label('subject', 'Subject:') !!}
    {!! Form::text('subject', $subject, ['class' => 'form-control','placeholder'=>'Subject of the Mail' ,'required'=>'required']) !!}
</div>

<!-- Message Field -->
{{-- <div class="form-group col-sm-12">
    {!! Form::label('message', 'Message:') !!}
    {!! Form::textarea('message', null, ['class' => 'form-control']) !!}
</div> --}}
<div class="form-group col-sm-12">
    {!! Form::label('message', 'Message:') !!}
     <textarea name="message" class="form-control"  required="required">{{$message}} </textarea>

</div>
<div class="form-group col-sm-12">
    {!! Form::label('attachment_path', 'Attachment File Name:') !!}
    {!! Form::text('attachment_path', $attachment_path, ['class' => 'form-control', 'placeholder'=>'Just give the filename', 'required'=>'required']) !!}
    <span style="color: red;">Note: Just enter the file name only  </span><br>
    <span style="color: red;">Note: you can put the file in this path  {{storage_path('email')}} </span>
</div>



<!-- Submit Field -->
<div class=" col-sm-6" align="center">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('home') !!}" class="btn btn-primary">Cancel</a>
</div>