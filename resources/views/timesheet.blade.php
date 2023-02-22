<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Timesheet') }}
        </h2>
    </x-slot>

    <div id='calendar'></div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @foreach($timesheet as $timesheet_detail)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-1 d-flex justify-content-between">
                    <div class="p-6 text-gray-900">
                        <div>{{$timesheet_detail->date}}</div>
                        @if(isset($timesheet_detail->creator))
                            <div>Người tạo: {{$timesheet_detail->creator->name}}</div>
                        @endif
                        @foreach($timesheet_detail->tasks as $task)
                            <div>- {{$task->content}}</div>
                        @endforeach
                        <div>Khó khăn: {{$timesheet_detail->difficult}}</div>
                        <div>Dự định: {{$timesheet_detail->schedule}}</div>
                    </div>
                    <div>
                        <x-secondary-button
                            x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'edit-timesheet-{{$timesheet_detail->id}}')"
                        >{{ __('Edit') }}</x-secondary-button>
                        <x-danger-button
                            x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'confirm-timesheet-deletion-{{$timesheet_detail->id}}')"
                        >{{ __('Delete') }}</x-danger-button>
                        @if(Auth::user()->can('updateStatus', $timesheet_detail))
                            @if($timesheet_detail->status == 'pending')
                                <form method="post" action="/timesheet/{{$timesheet_detail->id}}/approve">
                                    @csrf
                                    @method('patch')
                                    <x-text-input id="status" type="hidden" name="status" value="approved"/>
                                    <input class="border border-gray-100 p-1 rounded bg-primary text-white" type="submit" value="approve">
                                </form>
                            @elseif($timesheet_detail->status == 'approved')
                                <button class="border border-gray-100 p-1 rounded bg-gray-500 text-white" disabled>approved</button>
                            @endif
                        @endif
                    </div>
                </div>
                <x-modal name="edit-timesheet-{{$timesheet_detail->id}}" :show="false" focusable>
                    <form method="post" action="/timesheet/{{$timesheet_detail->id}}" class="p-6">
                        @csrf
                        @method('patch')

                        <div class="row">
                            <x-input-label for="task[]" :value="__('Task')"/>
                            <div class="col-lg-12">
                                @foreach($timesheet_detail->tasks as $task)
                                    <div id="inputFormRow">
                                        <div class="input-group mb-3">
                                            <input type="text" name="task[]" class="form-control m-input"
                                                   value="{{$task->content}}" placeholder="Enter title"
                                                   autocomplete="off">
                                            <div class="input-group-append">
                                                <button id="removeRow" type="button" class="btn btn-danger">Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div id="newRow"></div>
                                <button id="addRow" type="button" class="btn btn-info">Add Task</button>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="difficult" :value="__('Khó khăn')"/>
                            <x-text-input id="difficult" class="block mt-1 w-full" type="text" name="difficult"
                                          :value="$timesheet_detail->difficult" required autofocus/>
                            <x-input-error :messages="$errors->get('difficult')" class="mt-2"/>
                        </div>
                        <div>
                            <x-input-label for="schedule" :value="__('Dự định sẽ làm trong ngày tiếp theo')"/>
                            <x-text-input id="schedule" class="block mt-1 w-full" type="text" name="schedule"
                                          :value="$timesheet_detail->schedule" required autofocus/>
                            <x-input-error :messages="$errors->get('schedule')" class="mt-2"/>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <x-secondary-button x-on:click="$dispatch('close')">
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-danger-button class="ml-3">
                                {{ __('Update') }}
                            </x-danger-button>
                        </div>
                    </form>
                </x-modal>
                <x-modal name="confirm-timesheet-deletion-{{$timesheet_detail->id}}" :show="false" focusable>
                    <form method="post" action="/timesheet/{{$timesheet_detail->id}}" class="p-6">
                        @csrf
                        @method('delete')
                        <div>
                            Delete this timesheet?
                        </div>
                        <div class="mt-6 flex justify-end">
                            <x-secondary-button x-on:click="$dispatch('close')">
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-danger-button class="ml-3">
                                {{ __('Delete') }}
                            </x-danger-button>
                        </div>
                    </form>
                </x-modal>

            @endforeach
        </div>
    </div>

</x-app-layout>

<script type="text/javascript">
    // add row
    $("#addRow").click(function () {
        var html = '';
        html += '<div id="inputFormRow">';
        html += '<div class="input-group mb-3">';
        html += '<input type="text" name="task[]" class="form-control m-input" placeholder="Enter title" autocomplete="off">';
        html += '<div class="input-group-append">';
        html += '<button id="removeRow" type="button" class="btn btn-danger">Remove</button>';
        html += '</div>';
        html += '</div>';

        $('#newRow').append(html);
    });

    // remove row
    $(document).on('click', '#removeRow', function () {
        $(this).closest('#inputFormRow').remove();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: [
                    @foreach($timesheet as $t)
                {
                    title: '{{$t->schedule}}',
                    start: '{{$t->date}}',
                    groupId: '{{$t->id}}',
                    url: '/timesheet/{{$t->id}}'
                },
                @endforeach
            ],
            eventClick: function (event) {
                console.log(event.event._def)
                $("#timesheetDetail").modal('show')
            },
        });
        calendar.render();
    });

</script>
