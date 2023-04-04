@extends('layouts.master')
@section('title')
الفواتير الغير مدفوعه
@endsection
@section('css')
@include('layouts.tablecss')
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">الفواتير</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ الفواتير الغير مدفوعه</span>
						</div>
					</div>
					
				</div>
				<!-- breadcrumb -->
@endsection

				

@section('content')
				@if (session()->has('delete_invoice'))
        <script>
            window.onload = function() {
                notif({
                    msg: "تم حذف الفاتورة بنجاح",
                    type: "success"
                })
            }
        </script>
    @endif

					<div class="row">

					<!--div-->
					
					<div class="col-xl-12">
						<div class="card mg-b-20">
							<div class="card-header pb-0">
								<div class="d-flex justify-content-between">
								<a href="{{route('invoices.create')}}" class=" btn btn-sm btn-primary" style="color:white"> <i
                                class="fas fa-plus"></i>&nbsp; اضافة فاتورة</a>
									<i class="mdi mdi-dots-horizontal text-gray""></i>
								</div>
							</div>
							<div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table key-buttons text-md-nowrap" data-page-length='50'style="text-align: center">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">#</th>
                                    <th class="border-bottom-0">رقم الفاتورة</th>
                                    <th class="border-bottom-0">تاريخ القاتورة</th>
                                    <th class="border-bottom-0">تاريخ الاستحقاق</th>
                                    <th class="border-bottom-0">المنتج</th>
                                    <th class="border-bottom-0">القسم</th>
                                    <th class="border-bottom-0">الخصم</th>
                                    <th class="border-bottom-0">نسبة الضريبة</th>
                                    <th class="border-bottom-0">قيمة الضريبة</th>
                                    <th class="border-bottom-0">الاجمالي</th>
                                    <th class="border-bottom-0">الحالة</th>
                                    <th class="border-bottom-0">ملاحظات</th>
                                    <th class="border-bottom-0">العمليات</th>
                                </tr>
                            </thead>
										<tbody>
											@php
											$i=0
											@endphp
										@foreach($invoices as $invoice)
											@php
											$i++
											@endphp
											<tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $invoice->invoice_number}} </td>
                                        <td>{{ $invoice->invoice_Date }}</td>
                                        <td>{{ $invoice->due_date}}</td>
                                        <td>{{ $invoice->product}}</td>
										<td><a
                                        href="{{ url('InvoicesDetails') }}/{{ $invoice->id }}">{{ $invoice->sections->section_name }}</a>
                                        </td>
                                        <td>{{ $invoice->discount}}</td>
                                        <td>{{ $invoice->rate_vat}}</td>
                                        <td>{{ $invoice->value_vat}}</td>
                                        <td>{{ $invoice->Total}}</td>
                                        <td>
                                            @if ($invoice->value_status == 1)
                                                <span class="text-success">{{ $invoice->Status }}</span>
                                            @elseif($invoice->value_status == 2)
                                                <span class="text-danger">{{ $invoice->Status }}</span>
                                            @else
                                                <span class="text-warning">{{ $invoice->Status }}</span>
                                            @endif

                                        </td>
                                        <td>{{ $invoice->note}}</td>
										<td>
                                            <div class="dropdown">
                                                <button aria-expanded="false" aria-haspopup="true"
                                                    class="btn ripple btn-primary btn-sm" data-toggle="dropdown"
                                                    type="button">العمليات<i class="fas fa-caret-down ml-1"></i></button>
                                                <div class="dropdown-menu tx-13">
                                                        <a class="dropdown-item"
                                                            href="{{route('invoices.edit',$invoice->id)}}">تعديل
                                                            الفاتورة</a>
                                                
                                                        <a class="dropdown-item" href="#" data-invoice_id="{{ $invoice->id }}"
                                                            data-toggle="modal" data-target="#delete_invoice"><i
                                                                class="text-danger fas fa-trash-alt"></i>&nbsp;&nbsp;حذف
                                                            الفاتورة</a>

                                                        <a class="dropdown-item"
                                                            href="{{route('invoices.show',$invoice->id)}}"><i
                                                                class=" text-success fas fa-money-bill"></i>&nbsp;&nbsp;تغير حالة
                                                            الدفع</a>

                                                        <a class="dropdown-item" href="#" data-invoice_id="{{ $invoice->id }}"
                                                            data-toggle="modal" data-target="#Transfer_invoice"><i
                                                                class="text-warning fas fa-exchange-alt"></i>&nbsp;&nbsp;نقل الي
                                                            الارشيف</a>

                                                
                                                        <a class="dropdown-item" href="Print_invoice/{{ $invoice->id }}"><i
                                                                class="text-success fas fa-print"></i>&nbsp;&nbsp;طباعة
                                                            الفاتورة
                                                        </a>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
											@endforeach
											
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!--/div-->
                    </div>
					 <!-- حذف الفاتورة -->
    <div class="modal fade" id="delete_invoice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">حذف الفاتورة</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <form action="{{ route('invoices.destroy', 'test') }}" method="post">
                        {{ method_field('delete') }}
                        {{ csrf_field() }}
                </div>
                <div class="modal-body">
                    هل انت متاكد من عملية الحذف ؟
                    <input type="hidden" name="invoice_id" id="invoice_id" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                    <button type="submit" class="btn btn-danger">تاكيد</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
@include('layouts.tablejs')
<script>
        $('#delete_invoice').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var invoice_id = button.data('invoice_id')
            var modal = $(this)
            modal.find('.modal-body #invoice_id').val(invoice_id);
        })
    </script>

@endsection