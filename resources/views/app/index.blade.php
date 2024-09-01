@extends('layouts.app')
@section('content')
    <div class="title">
        <h2>Project List</h2>
        @can('project-create')
            <button class="open-modal-btn">Create</button>
        @endcan
    </div>
    <form id="searchForm" class="search-table">
        <fieldset>
            <label for="">Name</label>
            <input type="text" id="name" placeholder="Name" class="mb-0"/>
        </fieldset>
        <button type="submit">Search</button>
    </form>
    <div class="table-responsive">
        <table id="table-design">
            <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
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
                <h2>Add Project</h2>
                <span class="close">×</span>
            </div>
            <div id="modal-errors"></div>
            <form id="modalForm">
                <input type="text" id="modal-name" placeholder="Text"/>
                <textarea id="modal-description" rows="5" placeholder="Content"></textarea>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>

@endsection

@section('js')
    <script type="module">
        const userPermissions = @json(auth()->user()->getAllPermissions()->pluck('name'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const modal = document.getElementById('myModal');
        const closeModalBtn = modal.querySelector('.close');
        const openModalBtn = document.querySelector('.open-modal-btn');
        const form = document.getElementById('modalForm');
        const searchForm = document.getElementById('searchForm');
        let currentItemId = null;

        // open edit modal function
        function openModal(name, description, id) {
            modal.querySelector('.modal-title h2').textContent = "Edit Project";
            document.getElementById('modal-name').value = name;
            document.getElementById('modal-description').value = description;
            document.getElementById('modal-errors').innerHTML = '';
            currentItemId = id;
            modal.style.display = 'block';
        }

        // open add modal function
        openModalBtn.onclick = function () {
            modal.querySelector('.modal-title h2').textContent = "Add Project";
            document.getElementById('modal-name').value = '';
            document.getElementById('modal-description').value = '';
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

        // URL of the API endpoint
        const apiUrl = '/project';

        // create and update form
        form.onsubmit = function (event) {
            event.preventDefault();
            const name = document.getElementById('modal-name').value;
            const description = document.getElementById('modal-description').value;
            currentItemId ? update(name, description) : create(name, description);
        };

        searchForm.onsubmit = function (event) {
            event.preventDefault();
            const name = document.getElementById('name').value;

            fetch(`${apiUrl}/search?name=${encodeURIComponent(name)}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            }).then(response => response.json())
                .then(data => {
                    fetchDataTable(data);
                })

        };

        const create = (name, description) => {
            fetch(`${apiUrl}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    name,
                    description,
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

        const update = (name, description) => {
            fetch(`${apiUrl}/${currentItemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    name,
                    description,
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

        window.Echo.channel('projects')
            .listen('.project.created', (e) => {
                const rowElement = document.createElement('tr');
                rowElement.setAttribute('data-id', e.data.id);
                const nameCell = document.createElement('td');
                nameCell.textContent = e.data.name;

                rowElement.appendChild(nameCell);

                const descriptionCell = document.createElement('td');
                descriptionCell.textContent = e.data.description;
                rowElement.appendChild(descriptionCell);

                // "Action"
                const actionCell = document.createElement('td');
                const actionButtonsDiv = document.createElement('div');
                actionButtonsDiv.className = 'action-buttons';

                // Edit button
                if (userPermissions.includes('project-update')) {
                    const editButton = document.createElement('button');
                    editButton.textContent = 'Edit';
                    editButton.className = 'edit';
                    editButton.onclick = () => openModal(e.data.name, e.data.description, e.data.id);
                    actionButtonsDiv.appendChild(editButton);
                }

                // View button
                if (userPermissions.includes('project-show')) {
                    const viewButton = document.createElement('a');
                    viewButton.href = `/detail/${e.data.id}`;
                    viewButton.textContent = 'View';
                    viewButton.className = 'info';
                    actionButtonsDiv.appendChild(viewButton);
                }

                if (userPermissions.includes('project-delete')) {
                    // Delete button
                    const deleteButton = document.createElement('button');
                    deleteButton.textContent = 'Delete';
                    deleteButton.className = 'delete';
                    deleteButton.addEventListener('click', () => {
                        if (confirm('Are you sure you want to delete this item?')) {
                            fetch(`/project/${e.data.id}`, {
                                method: 'DELETE',
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
                document.querySelector('#table-design tbody').prepend(rowElement);
            })
            .listen('.project.updated', (e) => {
                const rowToUpdate = document.querySelector(`tr[data-id="${e.data.id}"]`);
                if (rowToUpdate) {
                    const cells = rowToUpdate.querySelectorAll('td');
                    cells[0].innerText = e.data.name;
                    cells[1].innerText = e.data.description;
                }
            })
            .listen('.project.deleted', (e) => {
                const rowToDelete = document.querySelector(`tr[data-id="${e.data.id}"]`);
                if (rowToDelete) {
                    rowToDelete.remove();
                }
            })

        // Fetch data
        const fetchData = (page = 1) => {
            fetch(`/project?page=${page}`)
                .then(response => response.json())
                .then(data => {
                    fetchDataTable(data)
                })
        }


        const fetchDataTable = (data) => {
            const tableBody = document.querySelector('#table-design tbody');
            tableBody.innerHTML = '';

            data.data?.forEach(item => {
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

                // "Action"
                const actionCell = document.createElement('td');
                const actionButtonsDiv = document.createElement('div');
                actionButtonsDiv.className = 'action-buttons';

                if (userPermissions.includes('project-update')) {
                    // Edit button
                    const editButton = document.createElement('button');
                    editButton.textContent = 'Edit';
                    editButton.className = 'edit';
                    editButton.onclick = () => openModal(item.name, item.description, item.id);
                    actionButtonsDiv.appendChild(editButton);
                }

                if (userPermissions.includes('project-show')) {
                    // View button
                    const viewButton = document.createElement('a');
                    viewButton.href = `/detail/${item.id}`;
                    viewButton.textContent = 'View';
                    viewButton.className = 'info';
                    actionButtonsDiv.appendChild(viewButton);
                }


                // Delete button
                if (userPermissions.includes('project-delete')) {
                    const deleteButton = document.createElement('button');
                    deleteButton.textContent = 'Delete';
                    deleteButton.className = 'delete';
                    deleteButton.addEventListener('click', () => {
                        if (confirm('Are you sure you want to delete this item?')) {
                            fetch(`/project/${item.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                            })
                                .then(response => {
                                    if (response.ok) {
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

@section('css')

@endsection











