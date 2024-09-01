@extends('layouts.app')
@section('content')
    <div class="title-box">
        <h2></h2>
        <p></p>
    </div>
    <div class="title">
        <h2>Task Management</h2>
        @can('task-create')
            <button type="button" class="open-modal-btn">Create</button>
        @endcan
    </div>
    <form id="searchForm" class="search-table">
        <fieldset>
            <label for="">Name</label>
            <input type="text" id="name" placeholder="Name" class="mb-0" />
        </fieldset>
        <fieldset>
            <label for="">Status</label>
            <select  name="status" id="status" class="mb-0 bg-white">
                <option value="">Choose</option>
                <option value="1">todo</option>
                <option value="2">in-progress</option>
                <option value="3">done</option>
            </select>
        </fieldset>
        <button type="submit">Search</button>
    </form>
    <div class="table-responsive">
        <table id="table-design">
            <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <div class="modal-title">
                <h2>Add Task</h2>
                <span class="close">×</span>
            </div>
            <div id="modal-errors"></div>
            <form id="modalForm">
                <input type="text" id="modal-name" placeholder="Text"/>
                <textarea id="modal-description" rows="5" placeholder="Content"></textarea>
                <select id="modal-select">
                    <option value="">Choose</option>
                    <option value="1">todo</option>
                    <option value="2">in-progress</option>
                    <option value="3">done</option>
                </select>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('js')
    <script type="module">
        const userPermissions = @json(auth()->user()->getAllPermissions()->pluck('name'));
        const currentUrl = window.location.href;
        const url = new URL(currentUrl);
        const pathname = url.pathname;
        const parts = pathname.split('/');
        const project_id = parts[parts.length - 1];
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const modal = document.getElementById('myModal');
        const closeModalBtn = modal.querySelector('.close');
        const openModalBtn = document.querySelector('.open-modal-btn');
        const form = document.getElementById('modalForm');
        const searchForm = document.getElementById('searchForm');
        let currentItemId = null;

        // open edit modal function
        function openModal(name, description, status, id) {
            modal.querySelector('.modal-title h2').textContent = "Edit Task";
            document.getElementById('modal-name').value = name;
            document.getElementById('modal-description').value = description;
            document.getElementById('modal-select').value = status;
            document.getElementById('modal-errors').innerHTML = '';
            currentItemId = id;
            modal.style.display = 'block';
        }

        // open add modal function
        openModalBtn.onclick = function () {
            modal.querySelector('.modal-title h2').textContent = "Add Task";
            document.getElementById('modal-name').value = '';
            document.getElementById('modal-description').value = '';
            document.getElementById('modal-select').value = '';
            document.getElementById('modal-errors').innerHTML = '';
            currentItemId = null;
            modal.style.display = 'block';
        };

        // close modal function
        closeModalBtn.onclick = function () {
            modal.style.display = 'none';
        };

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        };

        // create and update form
        form.onsubmit = function (event) {
            event.preventDefault();
            const name = document.getElementById('modal-name').value;
            const description = document.getElementById('modal-description').value;
            const status = document.getElementById('modal-select').value;
            currentItemId ? update(name, description, status) : create(name, description, status);
        };

        searchForm.onsubmit = function (event) {
            event.preventDefault();
            const name = document.getElementById('name').value;
            const status = document.getElementById('status').value;

            fetch(`/task/search?name=${encodeURIComponent(name)}&status=${status}&project_id=${project_id}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            }).then(response => response.json())
                .then(data => {
                    fetchDataTable(data.data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });

        };

        const create = (name, description, status) => {
            fetch(`/task`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    project_id,
                    name,
                    description,
                    status
                }),
            }).then(response => response.json())
                .then(data => {
                    if (data.errors) {
                        let errorsHtml = '';
                        for (let key in data.errors) {
                            errorsHtml += `<p>${data.errors[key].join(', ')}</p>`;
                        }
                        document.getElementById('modal-errors').innerHTML = errorsHtml;
                    } else {
                        modal.style.display = 'none';
                    }
                })
        }

        const update = (name, description, status) => {
            fetch(`/task/${currentItemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    project_id,
                    name,
                    description,
                    status
                }),
            }).then(response => response.json())
                .then(data => {
                    if (data.errors) {
                        let errorsHtml = '';
                        for (let key in data.errors) {
                            errorsHtml += `<p>${data.errors[key].join(', ')}</p>`;
                        }
                        document.getElementById('modal-errors').innerHTML = errorsHtml;
                    } else {
                        modal.style.display = 'none';
                    }
                })
        }

        window.Echo.channel('tasks')
            .listen('.task.created', (e) => {
                const rowElement = document.createElement('tr');
                rowElement.setAttribute('data-id', e.data.id);
                const nameCell = document.createElement('td');
                nameCell.textContent = e.data.name;

                rowElement.appendChild(nameCell);

                const descriptionCell = document.createElement('td');
                descriptionCell.textContent = e.data.description;
                rowElement.appendChild(descriptionCell);

                // "Status" field
                const statusCell = document.createElement('td');
                let status='';
                if(e.data.status==="1")
                    status='TODO'
                else if(e.data.status==="2")
                    status='IN_PROGRESS'
                else
                    status='DONE'
                statusCell.textContent = status;
                rowElement.appendChild(statusCell);

                // "Action"
                const actionCell = document.createElement('td');
                const actionButtonsDiv = document.createElement('div');
                actionButtonsDiv.className = 'action-buttons';

                // Edit button
                if (userPermissions.includes('task-update')) {
                    const editButton = document.createElement('button');
                    editButton.textContent = 'Edit';
                    editButton.className = 'edit';
                    console.log(e.data)
                    editButton.onclick = () => openModal(e.data.name, e.data.description, e.data.status, e.data.id);
                    actionButtonsDiv.appendChild(editButton);
                }

                // Delete button
                if (userPermissions.includes('task-delete')) {
                    const deleteButton = document.createElement('button');
                    deleteButton.textContent = 'Delete';
                    deleteButton.className = 'delete';
                    deleteButton.addEventListener('click', () => {
                        if (confirm('Are you sure you want to delete this item?')) {
                            fetch(`/task/${e.data.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                            })
                                .then(response => {
                                    if (response.ok) {
                                        rowElement.remove();
                                    } else {
                                        alert('Failed to delete item.');
                                    }
                                })
                        }
                    });
                    actionButtonsDiv.appendChild(deleteButton);
                }
                actionCell.appendChild(actionButtonsDiv);
                rowElement.appendChild(actionCell);
                document.querySelector('#table-design tbody').appendChild(rowElement);
            })
            .listen('.task.updated', (e) => {
                const rowToUpdate = document.querySelector(`tr[data-id="${e.data.id}"]`);
                if (rowToUpdate) {
                    const cells = rowToUpdate.querySelectorAll('td');
                    cells[0].innerText = e.data.name;
                    cells[1].innerText = e.data.description;
                    console.log(e.data)
                    let status = '';
                    if (e.data.status === "1")
                        status = 'TODO'
                    else if (e.data.status === "2")
                        status = 'IN_PROGRESS'
                    else
                        status = 'DONE'
                    cells[2].innerText = status;

                    const editButton = rowToUpdate.querySelector('.edit');
                    editButton.onclick = () => openModal(e.data.name, e.data.description, e.data.status, e.data.id);

                }
            })
            .listen('.task.deleted', (e) => {
                const rowToDelete = document.querySelector(`tr[data-id="${e.data.id}"]`);
                if (rowToDelete) {
                    rowToDelete.remove();
                }
            })

        // Fetch data
        const fetchData = () => {
            fetch(`/project/${project_id}`)
                .then(response => response.json()) // Parse the JSON from the response
                .then(data => {
                    const titleBox = document.querySelector('.title-box');
                    const title = titleBox.querySelector('h2');
                    const description = titleBox.querySelector('p');

                    if (data.data.name) {
                        title.textContent = data.data.name;
                    }
                    if (data.data.description) {
                        description.textContent = data.data.description;
                    }

                    fetchDataTable(data.data?.task);

                })
        }


        const fetchDataTable = (data) => {
            const tableBody = document.querySelector('#table-design tbody');
            //clear table data
            tableBody.innerHTML = '';
            data.forEach(item => {
                const row = document.createElement('tr');
                row.setAttribute('data-id', item.id);
                // "Name" field
                const nameCell = document.createElement('td');
                nameCell.textContent = item.name;
                row.appendChild(nameCell);

                // "Description" field
                const descCell = document.createElement('td');
                descCell.textContent = item.description;
                row.appendChild(descCell);

                // "Status" field
                const statusCell = document.createElement('td');
                let status = '';
                if (item.status === 1)
                    status = 'TODO'
                else if (item.status === 2)
                    status = 'IN_PROGRESS'
                else
                    status = 'DONE'
                statusCell.textContent = status;
                row.appendChild(statusCell);

                // "Action"
                const actionCell = document.createElement('td');
                const actionButtonsDiv = document.createElement('div');
                actionButtonsDiv.className = 'action-buttons';

                // Edit button
                if (userPermissions.includes('task-update')) {
                    const editButton = document.createElement('button');
                    editButton.href = '#'; // Edit URL
                    editButton.textContent = 'Edit';
                    editButton.className = 'edit';
                    editButton.onclick = () => openModal(item.name, item.description, item.status, item.id);
                    actionButtonsDiv.appendChild(editButton);
                }

                // Delete button
                if (userPermissions.includes('task-delete')) {
                    const deleteButton = document.createElement('button');
                    deleteButton.textContent = 'Delete';
                    deleteButton.className = 'delete';
                    deleteButton.addEventListener('click', () => {
                        if (confirm('Are you sure you want to delete this item?')) {
                            fetch(`/task/${item.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                            })
                                .then(response => {
                                    if (response.ok) {
                                        // Remove the row from the table
                                        row.remove();
                                    } else {
                                        alert('Failed to delete item.');
                                    }
                                })
                        }
                    });
                    actionButtonsDiv.appendChild(deleteButton);
                }

                actionCell.appendChild(actionButtonsDiv);
                row.appendChild(actionCell);

                tableBody.appendChild(row);
            });
        }

        // Refresh table on page load
        window.onload = fetchData;
    </script>
@endsection
