      
@include('components.validationerrors')

<?php $label_attributes = array('class'=>'col-sm-2 control-label') ?>
<div class="col-md-6">
  <div class="form-group {{ $errors->has('payment_date') ? 'has-error' : '' }}">
    {{ Form::label('payment_date', 'Payment Date', $label_attributes) }}
    <div class="col-sm-10">
      {{ Form::input('date', 'payment_date', isset($payment) ? null : date('Y-m-d'), array('class'=>'form-control')) }}
      {{ $errors->first('payment_date', '<span class="help-block">:message</span>') }}
    </div>
  </div>

  <div class="form-group {{ $errors->has('company') ? 'has-error' : '' }}">
    {{ Form::label('company', 'Company', $label_attributes) }}
    <div class="col-sm-10">
      {{ Form::text('company', null, array('class'=>'form-control', 'placeholder'=>'Company')) }}
      {{ $errors->first('company', '<span class="help-block">:message</span>') }}
    </div>
  </div>

  <div class="form-group {{ $errors->has('item') ? 'has-error' : '' }}">
    {{ Form::label('item', 'Item', $label_attributes) }}
    <div class="col-sm-10">
      {{ Form::text('item', null, array('class'=>'form-control', 'placeholder'=>'Item')) }}
      {{ $errors->first('item', '<span class="help-block">:message</span>') }}
    </div>
  </div>

  <div class="form-group {{ $errors->has('comment') ? 'has-error' : '' }}">
    {{ Form::label('comment', 'Comment', $label_attributes) }}
    <div class="col-sm-10">
      {{ Form::textarea('comment', null, array('class'=>'form-control', 'placeholder'=>'Optional description of the payment')) }}
      {{ $errors->first('comment', '<span class="help-block">:message</span>') }}
    </div>

  </div>

</div>
<div class="col-md-6">
  <legend>Payer Details</legend>
  @foreach ($payers as $payer)

  <div class="form-group">
    <?php 
    $amount = isset($pps[$payer->id]) ? $pps[$payer->id]['amount'] : number_format(0,2); 
    $pays = isset($pps[$payer->id]) ? $pps[$payer->id]['pays'] : true; 
    ?>
    {{ Form::label($payer->id . '-amount', $payer->name, $label_attributes) }} 
    <div class="col-sm-6">
      {{ Form::input('number', $payer->id . '-amount', $amount, array('class'=>'form-control', 'step' => 'any', 'value' => 0)) }}
    </div>
    <div class="col-sm-4 control-label">
      <label>{{ Form::checkbox($payer->id . '-pays', $payer->id . '-pays', $pays) }} Pays</label>
    </div>
  </div>


  <br/>
  @endforeach
</div>

