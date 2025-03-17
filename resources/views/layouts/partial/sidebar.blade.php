<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
      <!--begin::Brand Link-->
      <a href="#" class="brand-link">
        <!--begin::Brand Image-->
        <img
          src="/dist/assets/img/user2-160x160.jpg"
          alt="AdminLTE Logo"
          class="brand-image opacity-75 shadow"
        />
        <!--end::Brand Image-->
        <!--begin::Brand Text-->
        <span class="brand-text fw-light">AdminLTE 4</span>
        <!--end::Brand Text-->
      </a>
      <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
      <nav class="mt-2">
        <!--begin::Sidebar Menu-->
        <ul
          class="nav sidebar-menu flex-column"
          data-lte-toggle="treeview"
          role="menu"
          data-accordion="false"
        >
          <li class="nav-item menu-open">
            <a href="" class="nav-link active">
              <i class="nav-icon bi bi-speedometer"></i>
              <p>
                Dashboard
                
              </p>
            </a>
          </li>


          <li class="nav-item menu-open">
            <a href="" class="nav-link active">
              <i class="nav-icon bi bi-speedometer"></i>
              <p>
                Your Connections
                
              </p>
            </a>
          </li>




          
          {{-- <li class="nav-item">
            <a href="" class="nav-link">
              <i class="nav-icon bi bi-box-seam-fill"></i>
              <p>
                Add Category
                
              </p>
            </a>
            
          </li>
          <li class="nav-item">
            <a href="" class="nav-link">
              <i class="nav-icon bi bi-clipboard-fill"></i>
              <p>
                Subcategories
                
                
              </p>
            </a>
            
          </li> --}}
 
          
        </ul>



        {{-- connections list --}}

        {{-- <ul id="connectionsList" style="background: white"></ul> <!-- Connections List -->

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <script>
            $(document).ready(function () {
                function fetchConnections() {
                    $.ajax({
                        url: "{{ route('chat.conn') }}", // API Route
                        type: "GET",
                        dataType: "json",
                        success: function (response) {
                            if (response.status === "success") {
                                let connectionsList = $("#connectionsList");
                                connectionsList.html(""); // Clear old data
        
                                if (response.connections.length === 0) {
                                    connectionsList.append("<li>No connections found.</li>");
                                } else {
                                    response.connections.forEach(function (connection) {
                                        let currentUserId = {{ auth()->id() }}; // Logged-in user ID
        
                                        // Determine the other user (not the logged-in user)
                                        let otherUser = (connection.sender_id == currentUserId) 
                                            ? connection.receiver 
                                            : connection.sender;
        
                                        let connectionHtml = `
                                        <a href="/chat/view/${ otherUser.id }">
                                            <li>
                                                <strong>${otherUser.name}</strong> 
                                                <span>Last Message: ${connection.last_message}</span>
                                                <small>${new Date(connection.last_message_time).toLocaleString()}</small>
                                            </li>
                                            </a>
                                        `;
                                        connectionsList.append(connectionHtml);
                                    });
                                }
                            }
                        },
                        error: function (xhr) {
                            console.log("Error fetching connections:", xhr);
                        }
                    });
                }
        
                fetchConnections(); // Initial fetch
                setInterval(fetchConnections, 5000); // Refresh connections every 5 seconds
            });
        </script> --}}



        <!-- Connections List -->
<ul id="connectionsList" class="list-group p-2" style=" border-radius: 10px;"></ul>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        function fetchConnections() {
            $.ajax({
                url: "{{ route('chat.conn') }}", // API Route
                type: "GET",
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        let connectionsList = $("#connectionsList");
                        connectionsList.html(""); // Clear old data

                        if (response.connections.length === 0) {
                            connectionsList.append("<li class='list-group-item text-center'>No connections found.</li>");
                        } else {
                            response.connections.forEach(function (connection) {
                                let currentUserId = {{ auth()->id() }}; // Logged-in user ID

                                // Determine the other user (not the logged-in user)
                                let otherUser = (connection.sender_id == currentUserId) 
                                    ? connection.receiver 
                                    : connection.sender;

                                // Check profile picture (If null, use default)
                                let profilePicture = otherUser.profile_picture 
                                    ? `/uploads/profile/${otherUser.profile_picture}` 
                                    : `https://via.placeholder.com/50`;

                                // Format last message (First 5 letters + ...)
                                let lastMessage = connection.last_message.length > 5 
                                    ? connection.last_message.substring(0, 5) + "..." 
                                    : connection.last_message;

                                let connectionHtml = `
                                <a href="/chat/view/${otherUser.id}" class="text-decoration-none">
                                    <li class="list-group-item d-flex align-items-center p-3 rounded" style="background: #f8f9fa; margin-bottom: 10px;">
                                        <img src="${profilePicture}" alt="Profile" class="rounded-circle me-3" width="20" height="20">
                                        <div class="flex-grow-1">
                                            <strong class="d-block" style="color: #333;">${otherUser.name}</strong><br>
                                            <span class="text-muted small"> ${lastMessage}</span><br>
                                        </div>
                                        <small class="text-muted">${new Date(connection.last_message_time).toLocaleString()}</small>
                                    </li>
                                </a>
                                `;
                                connectionsList.append(connectionHtml);
                            });
                        }
                    }
                },
                error: function (xhr) {
                    console.log("Error fetching connections:", xhr);
                }
            });
        }

        fetchConnections(); // Initial fetch
        setInterval(fetchConnections, 5000); // Refresh connections every 5 seconds
    });
</script>

        
        

        {{-- logout section --}}
        <div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf

            <x-dropdown-link :href="route('logout')"
                    onclick="event.preventDefault();
                                this.closest('form').submit();">
                {{ __('Log Out') }}
            </x-dropdown-link>
        </form>
        </div>
      
      


        {{-- <div class="container">
          <h3>Recent Chats</h3>
          < class="chat-list">
              @foreach($connections as $connection)
                  @php
                      $chatUser = ($connection->sender_id == auth()->id()) ? $connection->receiver : $connection->sender;
                  @endphp
                  <li>
                      <a href="{{ route('chat.private', $chatUser->id) }}">
                          <strong>{{ $chatUser->name }}</strong>
                          <p>{{ $connection->last_message }}</p>
                          <span>{{ $connection->last_message_time->diffForHumans() }}</span>
                      </a>
                  </li>
              @endforeach
          
      </div> --}}
        <!--end::Sidebar Menu-->
      </nav>
    </div>
    <!--end::Sidebar Wrapper-->
  </aside>