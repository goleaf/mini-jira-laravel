   
                <tr>
                    <form method="GET" action="{{ route('tasks.index') }}">
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="creationDateDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ __('creation_date') }}
                                </button>
                                <div class="dropdown-menu p-3" aria-labelledby="creationDateDropdown">
                                    <div class="mb-2">
                                        <label for="created_at_from" class="form-label">{{ __('from') }}</label>
                                        <input type="date" class="form-control form-control-sm" id="created_at_from" name="created_at_from" value="{{ request('created_at_from') }}">
                                    </div>
                                    <div class="mb-2">
                                        <label for="created_at_to" class="form-label">{{ __('to') }}</label>
                                        <input type="date" class="form-control form-control-sm" id="created_at_to" name="created_at_to" value="{{ request('created_at_to') }}">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary">{{ __('apply') }}</button>
                                </div>
                            </div>
                            @if(request('created_at_from') || request('created_at_to'))
                                <script>
                                    document.getElementById('creationDateDropdown').innerHTML = '{{ __("creation_date") }}:<br>{{ __("from") }}: {{ request("created_at_from") }}<br>{{ __("to") }}: {{ request("created_at_to") }}';
                                </script>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="deadlineDateDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ __('deadline_date') }}
                                </button>
                                <div class="dropdown-menu p-3" aria-labelledby="deadlineDateDropdown">
                                    <div class="mb-2">
                                        <label for="task_deadline_date_from" class="form-label">{{ __('from') }}</label>
                                        <input type="date" class="form-control form-control-sm" id="task_deadline_date_from" name="task_deadline_date_from" value="{{ request('task_deadline_date_from') }}">
                                    </div>
                                    <div class="mb-2">
                                        <label for="task_deadline_date_to" class="form-label">{{ __('to') }}</label>
                                        <input type="date" class="form-control form-control-sm" id="task_deadline_date_to" name="task_deadline_date_to" value="{{ request('task_deadline_date_to') }}">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary">{{ __('apply') }}</button>
                                </div>
                            </div>
                            @if(request('task_deadline_date_from') || request('task_deadline_date_to'))
                                <script>
                                    document.getElementById('deadlineDateDropdown').innerHTML = '{{ __("deadline_date") }}:<br>{{ __("from") }}: {{ request("task_deadline_date_from") }}<br>{{ __("to") }}: {{ request("task_deadline_date_to") }}';
                                </script>
                            @endif
                        </td>
                        <td><input type="text" class="form-control form-control-sm" id="title" name="search" value="{{ request('search') }}" placeholder="{{ __('task_title') }}"></td>
                        <td>
                            <select class="form-select form-select-sm" id="task_creator_user_id" name="task_creator_user_id">
                                <option value="">{{ __('select') }}</option>
                                @foreach($taskCreators as $taskCreator)
                                    <option value="{{ $taskCreator->id }}" {{ request('task_creator_user_id') == $taskCreator->id ? 'selected' : '' }}>
                                        {{ $taskCreator->name }} ({{ $taskCreator->tasksCreated()->count() }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm" id="assigned_user_id" name="assigned_user_id">
                                <option value="">{{ __('select') }}</option>
                                @foreach($assignedUsers as $assignedUser)
                                    <option value="{{ $assignedUser->id }}" {{ request('assigned_user_id') == $assignedUser->id ? 'selected' : '' }}>
                                        {{ $assignedUser->name }} ({{ $assignedUser->tasksAssigned()->count() }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm" id="assigned_tester_user_id" name="assigned_tester_user_id">
                                <option value="">{{ __('select') }}</option>
                                @foreach($assignedTesters as $assignedTester)
                                    <option value="{{ $assignedTester->id }}" {{ request('assigned_tester_user_id') == $assignedTester->id ? 'selected' : '' }}>
                                        {{ $assignedTester->name }} ({{ $assignedTester->tasksAssigned()->count() }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm" id="task_status_id" name="task_status_id">
                                <option value="">{{ __('select') }}</option>
                                @foreach($taskStatuses as $taskStatus)
                                    <option value="{{ $taskStatus->id }}" {{ request('task_status_id') == $taskStatus->id ? 'selected' : '' }}>
                                        {{ $taskStatus->name }} ({{ $taskStatus->tasks()->count() }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm" id="task_type_id" name="task_type_id">
                                <option value="">{{ __('select') }}</option>
                                @foreach($taskTypes as $taskType)
                                    <option value="{{ $taskType->id }}" {{ request('task_type_id') == $taskType->id ? 'selected' : '' }}>
                                        {{ $taskType->name }} ({{ $taskType->tasks()->count() }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-outline-secondary btn-sm me-2"><i class="fas fa-filter me-2"></i>{{ __('filter') }}</button>
                            </div>
                        </td>
                    </form>
                </tr>