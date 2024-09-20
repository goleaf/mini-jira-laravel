   
                <tr>
                    <form method="GET" action="{{ route('tasks.index') }}">
                        <td><input type="date" class="form-control form-control-sm" id="created_at" name="created_at" value="{{ request('created_at') }}"></td>
                        <td><input type="date" class="form-control form-control-sm" id="task_deadline_date" name="task_deadline_date" value="{{ request('task_deadline_date') }}"></td>
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
                                <button type="submit" class="btn btn-outline-secondary btn-sm me-2"><i class="fas fa-filter me-2"></i>{{ __('Filter') }}</button>
                            </div>
                        </td>
                    </form>
                </tr>