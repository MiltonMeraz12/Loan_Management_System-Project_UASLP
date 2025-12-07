// Espera a que todo el contenido del HTML (DOM) se cargue antes de ejecutar el script
document.addEventListener('DOMContentLoaded', function() {

    // --- 1. SELECCIÓN DE ELEMENTOS ---

    // Selecciona los dos botones de navegación
    const tabButtons = document.querySelectorAll('.tab-button');
    
    // Selecciona todo el contenido que cambiará
    const statusCards = document.querySelectorAll('.status-seccion .status-card');
    const servicesSections = document.querySelectorAll('.services-section');
    const inventorySections = document.querySelectorAll('.inventory-section');

    
    // --- 2. ASIGNACIÓN DE EVENTOS ---
    
    // Recorre cada botón ("Consejería" y "FUP") y le añade un "escuchador" de clics
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            
            // --- 3. LÓGICA AL HACER CLIC ---

            // --- A. Manejar el estado 'activo' del botón ---
            
            // Primero, quita la clase 'activo' de TODOS los botones
            tabButtons.forEach(btn => {
                btn.classList.remove('activo');
            });
            
            // Luego, añade la clase 'activo' SOLO al botón que se presionó
            button.classList.add('activo');

            
            // --- B. Manejar la visibilidad del contenido ---

            // Obtiene el valor del atributo 'data-tab' (será "consejeria" o "fup")
            const targetTab = button.dataset.tab;
            
            // Construye los IDs de las 3 secciones que queremos MOSTRAR
            const targetStatusId = targetTab + '-status';
            const targetServicesId = targetTab + '-services';
            const targetInventoryId = targetTab + '-inventory';

            // Oculta TODOS los bloques de contenido
            statusCards.forEach(card => card.classList.add('hidden'));
            servicesSections.forEach(section => section.classList.add('hidden'));
            inventorySections.forEach(section => section.classList.add('hidden'));

            // Busca y MUESTRA (quitando 'hidden') solo las 3 secciones que coinciden con el botón
            const targetStatus = document.getElementById(targetStatusId);
            const targetServices = document.getElementById(targetServicesId);
            const targetInventory = document.getElementById(targetInventoryId);

            if (targetStatus) {
                targetStatus.classList.remove('hidden');
            }
            if (targetServices) {
                targetServices.classList.remove('hidden');
            }
            if (targetInventory) {
                targetInventory.classList.remove('hidden');
            }
        });
    });

    // =================================================
    // NUEVA LÓGICA PARA LAS PESTAÑAS DEL DASHBOARD
    // =================================================
    
    // 1. Selecciona los botones de navegación del dashboard
    const dashButtons = document.querySelectorAll('.dash-nav-button');
    
    // 2. Selecciona las páginas de contenido del dashboard
    const dashPages = document.querySelectorAll('.dash-page');

    // 3. Añade un "escuchador" de clics a cada botón del dashboard
    dashButtons.forEach(button => {
        button.addEventListener('click', () => {

            // --- A. Manejar estado 'activo' del botón ---
            
            // Quita 'activo' de todos los botones
            dashButtons.forEach(btn => {
                btn.classList.remove('activo');
            });
            // Añade 'activo' solo al botón presionado
            button.classList.add('activo');

            
            // --- B. Manejar visibilidad de la página ---
            
            // Obtiene el 'data-page' (ej. "prestamos", "inventario")
            const targetPage = button.dataset.page;
            const targetPageId = 'page-' + targetPage;

            // Oculta TODAS las páginas
            dashPages.forEach(page => {
                page.classList.add('hidden');
            });

            // Muestra SOLAMENTE la página correspondiente
            const target = document.getElementById(targetPageId);
            if (target) {
                target.classList.remove('hidden');
            }
        });
    });

    // =================================================
    // NUEVA LÓGICA PARA GESTIONAR INVENTARIO (MODALES)
    // =================================================
    
    // --- Selección de Elementos ---
    const openAddModalBtn = document.getElementById('open-add-modal-btn');
    const addItemModal = document.getElementById('add-item-modal');
    const closeAddModalBtn = document.getElementById('close-add-modal-btn');
    const addItemForm = document.getElementById('add-item-form'); // Formulario de agregar

    const editItemModal = document.getElementById('edit-item-modal');
    const closeEditModalBtn = document.getElementById('close-edit-modal-btn');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');
    const editItemForm = document.getElementById('edit-item-form'); // Formulario de editar

    const inventoryTable = document.querySelector('.inventory-table-manage'); // Contenedor de la tabla

    // --- Funciones para abrir/cerrar modales ---
    const openModal = (modal) => {
        if (modal) modal.classList.remove('hidden');
    };
    const closeModal = (modal) => {
        if (modal) modal.classList.add('hidden');
    };

    // --- Evento para ABRIR el modal de AGREGAR ---
    if (openAddModalBtn) {
        openAddModalBtn.addEventListener('click', () => {
            addItemForm.reset(); // Limpia el formulario al abrir
            openModal(addItemModal);
        });
    }

    // --- Evento para CERRAR el modal de AGREGAR (botón X) ---
    if (closeAddModalBtn) {
        closeAddModalBtn.addEventListener('click', () => {
            closeModal(addItemModal);
        });
    }

    // --- Evento para CERRAR el modal de EDITAR (botón X) ---
    if (closeEditModalBtn) {
        closeEditModalBtn.addEventListener('click', () => {
            closeModal(editItemModal);
        });
    }
    
    // --- Evento para CERRAR el modal de EDITAR (botón Cancelar) ---
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', () => {
             closeModal(editItemModal);
        });
    }

    // --- Evento para CERRAR modales haciendo clic FUERA ---
    window.addEventListener('click', (event) => {
        if (event.target === addItemModal) {
            closeModal(addItemModal);
        }
        if (event.target === editItemModal) {
            closeModal(editItemModal);
        }
    });

    // --- Lógica al ENVIAR el formulario de AGREGAR ---
    if (addItemForm) {
        addItemForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Evita que la página se recargue
            console.log("Artículo agregado (visualmente)");
            // Aquí iría la lógica para enviar los datos al servidor (PHP, etc.)
            closeModal(addItemModal); // Cierra el modal
        });
    }

    // --- Lógica al ENVIAR el formulario de EDITAR ---
     if (editItemForm) {
        editItemForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Evita que la página se recargue
            const itemId = document.getElementById('edit-item-id').value;
            console.log("Cambios guardados para item ID:", itemId, "(visualmente)");
            // Aquí iría la lógica para enviar los datos al servidor
            closeModal(editItemModal); // Cierra el modal
        });
    }

    // --- Eventos para botones EDITAR y ELIMINAR (usando delegación) ---
    if (inventoryTable) {
        inventoryTable.addEventListener('click', (event) => {
            
            // Si se hizo clic en un botón EDITAR
            if (event.target.closest('.edit-btn')) {
                const button = event.target.closest('.edit-btn');
                const itemId = button.dataset.id;
                const itemRow = button.closest('.inv-manage-item');

                // Simular carga de datos (en una app real, los traerías del servidor)
                const itemName = itemRow.querySelector('.item-name').textContent;
                const itemDesc = itemRow.querySelector('.col-desc').textContent;
                const itemTotal = itemRow.querySelector('.col-total').textContent;
                const itemAvailable = itemRow.querySelector('.col-disp').textContent;
                const itemCategoryTag = itemRow.querySelector('.category-tag');
                const itemCategory = itemCategoryTag.classList.contains('consejeria') ? 'consejeria' : 'fup';

                // Llenar el formulario del modal de edición
                document.getElementById('edit-item-id').value = itemId;
                document.getElementById('edit-item-name').value = itemName;
                document.getElementById('edit-item-desc').value = itemDesc;
                document.getElementById('edit-item-total').value = itemTotal;
                document.getElementById('edit-item-available').value = itemAvailable;
                document.getElementById('edit-item-category').value = itemCategory;

                console.log("Abriendo modal para editar item ID:", itemId);
                openModal(editItemModal);
            }

            // Si se hizo clic en un botón ELIMINAR
            if (event.target.closest('.delete-btn')) {
                const button = event.target.closest('.delete-btn');
                const itemId = button.dataset.id;
                const itemRow = button.closest('.inv-manage-item');
                const itemName = itemRow.querySelector('.item-name').textContent;

                // Pregunta de confirmación
                const confirmed = confirm(`¿Estás seguro de que deseas eliminar "${itemName}" (ID: ${itemId})?`);

                if (confirmed) {
                    console.log("Eliminando item ID:", itemId, "(visualmente)");
                    // Aquí iría la lógica para eliminar en el servidor
                    itemRow.remove(); // Elimina la fila de la tabla (solo visual)
                }
            }
        });
    }

    // ==========================================================
    // NUEVA LÓGICA PARA LA PÁGINA REGISTRAR NUEVO PRÉSTAMO
    // ==========================================================

    // --- Selección de Elementos (Solo si estamos en esa página) ---
    const registerLoanForm = document.getElementById('register-loan-form');
    
    // Solo ejecuta esta lógica si el formulario existe en la página actual
    if (registerLoanForm) {
        
        const categorySelect = document.getElementById('loan-category');
        const studentKeyInput = document.getElementById('student-key'); // Ya validado con pattern HTML
        const studentNameInput = document.getElementById('student-name');
        const loanItemSelect = document.getElementById('loan-item');
        const availableItemsTitle = document.getElementById('available-items-title');
        const itemCountBadge = document.getElementById('item-count');
        const itemsListConsejería = document.getElementById('items-list-consejeria');
        const itemsListFup = document.getElementById('items-list-fup');
        const proxyCheckbox = document.getElementById('proxy-requester-checkbox');
        const proxyDetailsSection = document.getElementById('proxy-details-section');
        const proxyNameInput = document.getElementById('proxy-name');
        const proxyPhoneInput = document.getElementById('proxy-phone');
        const studentPhoneInput = document.getElementById('student-phone');
        // El campo "Autorizado por" se deja como está en el HTML por ahora

        // --- Función para actualizar la lista de artículos disponibles y el dropdown ---
        const updateAvailableItems = () => {
            const selectedCategory = categorySelect.value;
            let visibleItemList, hiddenItemList, categoryName;

            // Determina qué lista mostrar/ocultar y el nombre
            if (selectedCategory === 'consejeria') {
                visibleItemList = itemsListConsejería;
                hiddenItemList = itemsListFup;
                categoryName = 'Consejería';
            } else {
                visibleItemList = itemsListFup;
                hiddenItemList = itemsListConsejería;
                categoryName = 'FUP';
            }

            // Muestra/oculta las listas correctas
            visibleItemList.classList.remove('hidden');
            hiddenItemList.classList.add('hidden');

            // Actualiza el título y el contador de artículos
            availableItemsTitle.textContent = `Artículos Disponibles - ${categoryName}`;
            const itemCount = visibleItemList.querySelectorAll('.available-item').length;
            itemCountBadge.textContent = `${itemCount} artículos`;

            // Limpia el dropdown de "Artículo a Prestar" (dejando el placeholder)
            loanItemSelect.innerHTML = '<option value="" disabled selected>Selecciona un artículo</option>';

            // Llena el dropdown con los artículos DISPONIBLES de la categoría seleccionada
            const items = visibleItemList.querySelectorAll('.available-item');
            items.forEach(item => {
                const name = item.querySelector('.item-name').textContent;
                const quantityText = item.querySelector('.item-quantity').textContent; // "Cantidad: X"
                const quantity = parseInt(quantityText.split(':')[1].trim()); // Extrae el número
                
                // Solo añade al dropdown si hay cantidad disponible
                if (quantity > 0) {
                    const option = document.createElement('option');
                    // Usamos el ID del item si lo tuviéramos, por ahora el nombre
                    // option.value = item.dataset.id; // Suponiendo que tuvieras data-id en el item div
                    option.value = name; // Usamos el nombre como valor temporal
                    option.textContent = `${name} (Cantidad: ${quantity})`;
                    loanItemSelect.appendChild(option);
                }
            });
        };

        // --- Evento: Cambiar la Categoría ---
        categorySelect.addEventListener('change', updateAvailableItems);

        // --- Evento: Formatear Nombre del Estudiante mientras escribe ---
        studentNameInput.addEventListener('input', () => {
            let value = studentNameInput.value;
            // 1. Quitar caracteres no permitidos (solo letras, ñ y espacios)
            value = value.replace(/[^a-zA-ZñÑ\s]/g, '');
            // 2. Convertir primera letra de cada palabra a mayúscula
            value = value.toLowerCase().replace(/\b[a-zñ]/g, char => char.toUpperCase());
            // 3. Reemplazar múltiples espacios con uno solo
            value = value.replace(/\s+/g, ' ');
            // 4. Actualizar el valor del input
            studentNameInput.value = value;
        });

        // --- Evento: Mostrar/Ocultar detalles del solicitante ---
        proxyCheckbox.addEventListener('change', () => {
            if (proxyCheckbox.checked) {
                proxyDetailsSection.classList.remove('hidden');
                // Hacer campos del solicitante requeridos
                proxyNameInput.required = true;
                proxyPhoneInput.required = true;
            } else {
                proxyDetailsSection.classList.add('hidden');
                // Quitar 'required' y limpiar valores
                proxyNameInput.required = false;
                proxyPhoneInput.required = false;
                proxyNameInput.value = ''; 
                proxyPhoneInput.value = '';
            }
        });

        // --- Evento: Formatear Nombre del Solicitante (similar al del estudiante) ---
        proxyNameInput.addEventListener('input', () => {
            let value = proxyNameInput.value;
            value = value.replace(/[^a-zA-ZñÑ\s]/g, '');
            value = value.toLowerCase().replace(/\b[a-zñ]/g, char => char.toUpperCase());
            value = value.replace(/\s+/g, ' ');
            proxyNameInput.value = value;
        });

        // --- Evento: Enviar el Formulario ---
        registerLoanForm.addEventListener('submit', (event) => {
            event.preventDefault(); 

            // --- VALIDACIÓN (Actualizada) ---
            
            // 1. Formatea nombres
            studentNameInput.dispatchEvent(new Event('input')); 
            proxyNameInput.dispatchEvent(new Event('input')); // Formatea también el proxy
            const studentName = studentNameInput.value.trim();
            const proxyName = proxyNameInput.value.trim(); 

            // 2. Verifica Clave Estudiante
            const studentKey = studentKeyInput.value;
            if (!/^\d{6}$/.test(studentKey)) {
                alert('Error: La Clave del Estudiante debe contener exactamente 6 dígitos.');
                studentKeyInput.focus();
                return; 
            }

            // 3. Verifica Nombre Estudiante (Dueño ID)
            const studentNameParts = studentName.split(' ').filter(part => part.length > 0); 
            if (studentNameParts.length < 2) {
                alert('Error: El Nombre del Estudiante (Dueño de ID) debe contener al menos un nombre y un apellido.');
                studentNameInput.focus();
                return; 
            }
            studentNameInput.value = studentNameParts.join(' '); 

            // 4. Verifica Teléfono Estudiante (Dueño ID) - 10 dígitos
            const studentPhone = studentPhoneInput.value;
             if (!/^\d{10}$/.test(studentPhone)) {
                alert('Error: El Número de Teléfono (Dueño de ID) debe contener exactamente 10 dígitos.');
                studentPhoneInput.focus();
                return;
            }

            // 5. Verifica si es Proxy y valida campos del Solicitante
            if (proxyCheckbox.checked) {
                 // 5a. Valida Nombre Solicitante (al menos 2 palabras)
                 const proxyNameParts = proxyName.split(' ').filter(part => part.length > 0);
                 if (proxyNameParts.length < 2) {
                     alert('Error: El Nombre del Solicitante debe contener al menos un nombre y un apellido.');
                     proxyNameInput.focus();
                     return;
                 }
                 proxyNameInput.value = proxyNameParts.join(' ');

                 // 5b. Valida Teléfono Solicitante (10 dígitos)
                 const proxyPhone = proxyPhoneInput.value;
                 if (!/^\d{10}$/.test(proxyPhone)) {
                    alert('Error: El Teléfono del Solicitante debe contener exactamente 10 dígitos.');
                    proxyPhoneInput.focus();
                    return;
                }
            }
            
            // 6. Verifica Artículo seleccionado
            if (!loanItemSelect.value) { 
                alert('Error: Debes seleccionar un Artículo a prestar.');
                loanItemSelect.focus();
                return;
            }
            
            // Si todo está bien...
            console.log("Registrando préstamo (visualmente)...");
            // Aquí iría la lógica real para enviar los datos al servidor

            alert('Préstamo registrado exitosamente (simulación).');
            registerLoanForm.reset(); 
            // Reset manual del checkbox y ocultar sección proxy si estaba visible
            proxyCheckbox.checked = false; 
            proxyDetailsSection.classList.add('hidden');
            proxyNameInput.required = false;
            proxyPhoneInput.required = false;

            updateAvailableItems(); 
        });

        // --- Inicializa la lista al cargar la página ---
        updateAvailableItems(); 

    } // Fin del if(registerLoanForm)

    // ==========================================================
    // NUEVA LÓGICA PARA LA PÁGINA PROCESAR DEVOLUCIÓN
    // ==========================================================

    const loanListContainer = document.getElementById('active-loans-list');
    
    // Solo ejecuta si estamos en la página de devoluciones
    if (loanListContainer) {
        
        // --- Select elements ---
        const searchInput = document.getElementById('loan-search-input');
        const returnPlaceholder = document.getElementById('return-placeholder');
        const returnDetailsSection = document.getElementById('return-details-section');
        const detailArticle = document.getElementById('detail-article');
        const detailStudent = document.getElementById('detail-student');
        const detailKey = document.getElementById('detail-key');
        const detailLoanDate = document.getElementById('detail-loan-date');
        const detailDueDate = document.getElementById('detail-due-date');
        const detailRegisteredBy = document.getElementById('detail-registered-by');
        const overdueWarning = document.getElementById('overdue-warning');
        const returnComments = document.getElementById('return-comments');
        const confirmReturnBtn = document.getElementById('confirm-return-btn');
        const cancelReturnBtn = document.getElementById('cancel-return-btn');
        const activeLoanCountBadge = document.getElementById('active-loan-count');
        const statTotalActive = document.getElementById('stat-total-active');
        const statOverdue = document.getElementById('stat-overdue');
        const statOnTime = document.getElementById('stat-on-time');
        const successBanner = document.getElementById('return-success-banner');
        const successMessageText = document.getElementById('success-message-text');
        

        let selectedLoanItem = null; // Variable to keep track of selected item

        // --- Function to update Quick Stats ---
        const updateQuickStats = () => {
            const allLoanItems = loanListContainer.querySelectorAll('.loan-item');
            const overdueItems = loanListContainer.querySelectorAll('.loan-item.overdue');
            const totalActive = allLoanItems.length;
            const totalOverdue = overdueItems.length;
            const totalOnTime = totalActive - totalOverdue;

            if (activeLoanCountBadge) activeLoanCountBadge.textContent = `${totalActive} préstamos`;
            if (statTotalActive) statTotalActive.textContent = totalActive;
            if (statOverdue) statOverdue.textContent = totalOverdue;
            if (statOnTime) statOnTime.textContent = totalOnTime;
        };

        // --- Function to reset the details view ---
        const resetDetailsView = () => {
             if (selectedLoanItem) {
                selectedLoanItem.classList.remove('selected');
                selectedLoanItem = null;
            }
            returnPlaceholder.classList.remove('hidden');
            returnDetailsSection.classList.add('hidden');
            returnComments.value = ''; // Clear comments
        };

        // --- Event: Click on a loan item in the list (Event Delegation) ---
        // --- Event: Click on a loan item in the list (Event Delegation) ---
        loanListContainer.addEventListener('click', (event) => {
            const clickedItem = event.target.closest('.loan-item');
            if (!clickedItem) return; 

            // Handle selection styling
            if (selectedLoanItem && selectedLoanItem !== clickedItem) {
                selectedLoanItem.classList.remove('selected');
            }
            clickedItem.classList.add('selected');
            selectedLoanItem = clickedItem;

            // Get data from data-* attributes
            const data = selectedLoanItem.dataset;
            
            // --- MOVED SELECTION INSIDE: Get detail elements *after* click ---
            const detailArticle = document.getElementById('detail-article');
            const detailStudent = document.getElementById('detail-student');
            const detailKey = document.getElementById('detail-key');
            const detailPhone = document.getElementById('detail-phone');
            const detailIdType = document.getElementById('detail-id-type');
            const detailLoanDate = document.getElementById('detail-loan-date');
            const detailDueDate = document.getElementById('detail-due-date');
            const detailRegisteredBy = document.getElementById('detail-registered-by');
            const overdueWarning = document.getElementById('overdue-warning');
            const proxyInfoDiv = document.getElementById('detail-proxy-info');
            const proxyNameSpan = document.getElementById('detail-proxy-name');
            const proxyPhoneSpan = document.getElementById('detail-proxy-phone');
            const returnPlaceholder = document.getElementById('return-placeholder'); // Moved here too
            const returnDetailsSection = document.getElementById('return-details-section'); // Moved here too
            const returnComments = document.getElementById('return-comments'); // Moved here too
            // --- END MOVED SELECTION ---

            // Populate details section (Check if elements exist before setting text)
            if(detailArticle) detailArticle.textContent = data.article || 'N/A';
            if(detailStudent) detailStudent.textContent = data.student || 'N/A';
            if(detailKey) detailKey.textContent = data.key || 'N/A';
            if(detailPhone) detailPhone.textContent = data.phone || 'N/A';
            if(detailIdType) detailIdType.textContent = data.idType || 'N/A';
            if(detailLoanDate) detailLoanDate.textContent = data.loanDate || 'N/A';
            if(detailDueDate) detailDueDate.textContent = data.dueDate || 'N/A';
            if(detailRegisteredBy) detailRegisteredBy.textContent = data.registeredBy || 'N/A'; 

            // Handle Proxy Info display
            if (proxyInfoDiv && proxyNameSpan && proxyPhoneSpan) {
                if (data.proxyName && data.proxyName.length > 0) {
                    proxyNameSpan.textContent = data.proxyName;
                    proxyPhoneSpan.textContent = data.proxyPhone || 'N/A';
                    proxyInfoDiv.classList.remove('hidden'); 
                } else {
                    proxyInfoDiv.classList.add('hidden'); 
                }
            }

            // Show/Hide overdue warning
            if (overdueWarning) {
                if (selectedLoanItem.classList.contains('overdue')) {
                    overdueWarning.classList.remove('hidden');
                } else {
                    overdueWarning.classList.add('hidden');
                }
            }

            // Show details section
            if(returnPlaceholder) returnPlaceholder.classList.add('hidden');
            if(returnDetailsSection) returnDetailsSection.classList.remove('hidden');
            if(returnComments) {
                returnComments.value = ''; // Clear comments
                returnComments.focus(); // Focus comments box
            }
        });

        // --- Event: Click Confirm Return button ---
        confirmReturnBtn.addEventListener('click', () => {
            if (!selectedLoanItem) {
                alert("Por favor, selecciona un préstamo de la lista primero.");
                return;
            }

            const data = selectedLoanItem.dataset;
            const comments = returnComments.value;
            
            console.log(`Devolviendo préstamo ID: ${data.loanId}, Comentarios: ${comments} (Visualmente)`);
            // --- Here would be the actual backend call to process the return ---

            // Show success banner
            successMessageText.textContent = `Préstamo devuelto exitosamente: ${data.article} por ${data.student}`;
            successBanner.classList.remove('hidden');

            // Remove item from list (visually)
            selectedLoanItem.remove();
            selectedLoanItem = null; // Clear selection

            // Reset view and update stats
            resetDetailsView();
            updateQuickStats();

            // Hide banner after a few seconds
            setTimeout(() => {
                successBanner.classList.add('hidden');
            }, 5000); // Hide after 5 seconds
        });

        // --- Event: Click Cancel button ---
        cancelReturnBtn.addEventListener('click', resetDetailsView);

        // --- Event: Filter list on search input ---
        searchInput.addEventListener('input', () => {
            const filterText = searchInput.value.toLowerCase().trim();
            const loanItems = loanListContainer.querySelectorAll('.loan-item');

            loanItems.forEach(item => {
                const itemText = item.textContent.toLowerCase();
                if (itemText.includes(filterText)) {
                    item.style.display = ''; // Show item
                } else {
                    item.style.display = 'none'; // Hide item
                }
            });
        });

        // --- Initial setup ---
        updateQuickStats(); // Calculate stats on page load

    } // Fin del if(loanListContainer)

    // ==========================================================
    // NUEVA LÓGICA PARA LA PÁGINA PRÉSTAMOS VENCIDOS
    // ==========================================================
    const overdueListContainer = document.getElementById('overdue-loans-list');

    // Solo ejecuta si estamos en la página de préstamos vencidos
    if (overdueListContainer) {

        // --- Select elements ---
        const confirmReturnModal = document.getElementById('confirm-return-modal');
        const closeConfirmModalBtn = document.getElementById('close-confirm-return-modal-btn');
        const cancelConfirmModalBtn = document.getElementById('cancel-confirm-return-btn');
        const confirmReturnForm = document.getElementById('confirm-return-form');
        const warningBanner = document.getElementById('overdue-warning-banner');
        const warningMessage = document.getElementById('warning-message-text');
        const overdueTotalCount = document.getElementById('overdue-total-count'); // Stat
        // Add selections for other stats if needed (Más Crítico, Promedio)

        // --- Function to update stats and banner ---
        const updateOverdueStats = () => {
            const overdueItems = overdueListContainer.querySelectorAll('.overdue-loan-item');
            const count = overdueItems.length;

            if (overdueTotalCount) overdueTotalCount.textContent = count;
            
            // Update banner text and visibility
            if (warningBanner && warningMessage) {
                if (count > 0) {
                    warningMessage.textContent = `Hay ${count} préstamo${count > 1 ? 's' : ''} vencido${count > 1 ? 's' : ''} que requieren atención inmediata.`;
                    warningBanner.classList.remove('hidden');
                } else {
                    warningBanner.classList.add('hidden');
                }
            }
             // Logic to calculate 'Más Crítico' and 'Promedio' would go here
             // You'd need to loop through overdueItems, get 'data-days-overdue', calculate max and average.
             // Example: document.getElementById('overdue-most-critical').textContent = maxDays + ' días';
             // Example: document.getElementById('overdue-average').textContent = avgDays + ' días';
        };

        // --- Selectores Adicionales para Modales ---
        const viewDetailsModal = document.getElementById('view-details-modal');
        const closeViewDetailsModalBtn = document.getElementById('close-view-details-modal-btn');
        const okViewDetailsBtn = document.getElementById('ok-view-details-btn'); // Botón 'Cerrar' del modal de detalles

        // --- Event Listener for Action Buttons (Delegation) ---
        overdueListContainer.addEventListener('click', (event) => {
            const viewDetailsButton = event.target.closest('.view-details-btn');
            const markReturnedButton = event.target.closest('.mark-returned-btn');
            const item = event.target.closest('.overdue-loan-item'); // Get the item row

            if (!item) return; // Si no se hizo clic en un item, salir
            
            const data = item.dataset; // Datos del item

            // --- Lógica para "Ver Detalles" ---
            if (viewDetailsButton) {
                console.log("Ver Detalles:", data);

                // Poblar el modal de Ver Detalles
                if (viewDetailsModal) {
                    document.getElementById('view-loan-id').textContent = data.loanId || 'N/A';
                    document.getElementById('view-article').textContent = data.article || 'N/A';
                    const categoryTag = item.querySelector('.category-tag'); // Obtener tag
                    document.getElementById('view-category').textContent = categoryTag ? categoryTag.textContent : 'N/A'; // Usar texto del tag
                    document.getElementById('view-loan-date').textContent = data.loanDate || 'N/A';
                    document.getElementById('view-due-date').textContent = data.dueDate || 'N/A';
                    document.getElementById('view-days-overdue').textContent = data.daysOverdue + (data.daysOverdue == 1 ? ' día' : ' días');
                    document.getElementById('view-registered-by').textContent = data.registeredBy || 'N/A';
                    
                    document.getElementById('view-student-name').textContent = data.student || 'N/A';
                    document.getElementById('view-student-key').textContent = data.key || 'N/A';
                    document.getElementById('view-student-phone').textContent = data.phone || 'N/A';
                    document.getElementById('view-id-type').textContent = data.idType || 'N/A';

                    // Mostrar/Ocultar sección Proxy
                    const proxySection = document.getElementById('view-proxy-section');
                    if (data.proxyName && data.proxyName.length > 0) {
                        document.getElementById('view-proxy-name').textContent = data.proxyName;
                        document.getElementById('view-proxy-phone').textContent = data.proxyPhone || 'N/A';
                        proxySection.classList.remove('hidden');
                    } else {
                        proxySection.classList.add('hidden');
                    }
                    
                    // Aquí podrías añadir comentarios si los tuvieras en data-*
                    // document.getElementById('view-loan-comments').textContent = data.comments || '';

                    openModal(viewDetailsModal); // Abrir modal de detalles
                }
            }

            // --- Lógica para "Marcar como Devuelto" ---
            if (markReturnedButton) {
                 console.log("Abrir modal Confirmar Devolución para:", data.loanId);
                // Poblar el modal de Confirmar Devolución (como ya lo hacía)
                if (confirmReturnModal) {
                    document.getElementById('modal-detail-article').textContent = data.article || 'N/A';
                    document.getElementById('modal-detail-student').textContent = data.student || 'N/A';
                    document.getElementById('modal-detail-key').textContent = data.key || 'N/A';
                    document.getElementById('modal-detail-loan-date').textContent = data.loanDate || 'N/A';
                    document.getElementById('modal-detail-due-date').textContent = data.dueDate || 'N/A';
                    document.getElementById('modal-detail-days-overdue').textContent = data.daysOverdue + (data.daysOverdue == 1 ? ' día' : ' días');
                    document.getElementById('confirm-loan-id').value = data.loanId; 
                    document.getElementById('modal-return-comments').value = ''; 

                    openModal(confirmReturnModal); // Abrir modal de confirmación
                }
            }
        });

        // --- Event Listener para Confirmar Devolución (SIN CAMBIOS) ---
        if (confirmReturnForm) {
            confirmReturnForm.addEventListener('submit', (event) => {
                event.preventDefault();
                const loanId = document.getElementById('confirm-loan-id').value;
                const comments = document.getElementById('modal-return-comments').value;

                console.log(`Confirmando devolución para Préstamo ID: ${loanId}, Comentarios: ${comments} (Visualmente)`);
                // --- Backend call ---

                const itemToRemove = overdueListContainer.querySelector(`.overdue-loan-item[data-loan-id="${loanId}"]`);
                if (itemToRemove) itemToRemove.remove();

                closeModal(confirmReturnModal); 
                updateOverdueStats(); 
                
                // Mostrar banner de éxito (opcional, necesitarías el banner en el HTML)
                 // const successBanner = document.getElementById('return-success-banner'); // O un banner específico para esta página
                 // const successMsg = document.getElementById('success-message-text');
                 // if(successBanner && successMsg){
                 //      const itemData = itemToRemove ? itemToRemove.dataset : {article: 'Artículo', student: 'Estudiante'}; // Fallback
                 //      successMsg.textContent = `Préstamo de ${itemData.article} por ${itemData.student} marcado como devuelto.`;
                 //      successBanner.classList.remove('hidden');
                 //      setTimeout(() => successBanner.classList.add('hidden'), 5000);
                 // }
            });
        }

        // --- Event Listeners para CERRAR Modales ---
        // Cerrar Modal Confirmar Devolución (X y Cancelar)
        if (closeConfirmModalBtn) closeConfirmModalBtn.addEventListener('click', () => closeModal(confirmReturnModal));
        if (cancelConfirmModalBtn) cancelConfirmModalBtn.addEventListener('click', () => closeModal(confirmReturnModal));
        
        // Cerrar Modal Ver Detalles (X y Cerrar)
        if (closeViewDetailsModalBtn) closeViewDetailsModalBtn.addEventListener('click', () => closeModal(viewDetailsModal));
        if (okViewDetailsBtn) okViewDetailsBtn.addEventListener('click', () => closeModal(viewDetailsModal));

        // Cerrar CUALQUIER modal al hacer clic fuera (YA EXISTE y funciona para ambos)
        window.addEventListener('click', (event) => {
             if (event.target === confirmReturnModal) closeModal(confirmReturnModal);
             if (event.target === viewDetailsModal) closeModal(viewDetailsModal); // Añadir para el nuevo modal
        });

        // --- (El resto de la lógica: updateOverdueStats, initial setup, etc. no cambia) ---
        // ...

        // --- Event Listeners to Close the Confirm Return Modal ---
        if (closeConfirmModalBtn) {
            closeConfirmModalBtn.addEventListener('click', () => closeModal(confirmReturnModal));
        }
        if (cancelConfirmModalBtn) {
            cancelConfirmModalBtn.addEventListener('click', () => closeModal(confirmReturnModal));
        }
        // Clicking outside the modal is handled by the existing window listener

        // --- Initial setup ---
        updateOverdueStats(); // Update banner and stats on load

    } // Fin del if(overdueListContainer)

    // ==========================================================
    // NUEVA LÓGICA PARA LA PÁGINA HISTORIAL DE PRÉSTAMOS
    // ==========================================================
    const historyFilterForm = document.getElementById('history-filter-form');

    // Solo ejecuta si estamos en la página de historial
    if (historyFilterForm) {

        // --- Select elements ---
        const searchTypeSelect = document.getElementById('search-type');
        const studentFilterGroups = document.querySelectorAll('.student-filter'); // Clave + Nombre
        const articleFilterGroup = document.querySelector('.article-filter'); // Artículo
        const historyTabButtons = document.querySelectorAll('.history-tab-button');
        const historyTabContents = document.querySelectorAll('.history-tab-content');
        const historyLoanList = document.getElementById('history-loans-list'); // Container for loan items
        const resultsCountSpan = document.getElementById('results-count'); // Span in tab
        const historyRecordCountSpan = document.getElementById('history-record-count'); // Span in header
        const clearFiltersBtn = document.getElementById('clear-filters-btn'); // Clear button

        // --- Event: Change Search Type (Estudiante/Artículo) ---
        searchTypeSelect.addEventListener('change', () => {
            const selectedType = searchTypeSelect.value;
            if (selectedType === 'estudiante') {
                studentFilterGroups.forEach(el => el.classList.remove('hidden'));
                if (articleFilterGroup) articleFilterGroup.classList.add('hidden');
                // Clear article input if needed
                const articleInput = document.getElementById('article-name-filter');
                if (articleInput) articleInput.value = '';
            } else { // 'articulo'
                studentFilterGroups.forEach(el => el.classList.add('hidden'));
                if (articleFilterGroup) articleFilterGroup.classList.remove('hidden');
                 // Clear student inputs if needed
                 const keyInput = document.getElementById('student-key-filter');
                 const nameInput = document.getElementById('student-name-filter');
                 if(keyInput) keyInput.value = '';
                 if(nameInput) nameInput.value = '';
            }
        });

        // --- Event: Switch Tabs (Resultados/Estadísticas) ---
        historyTabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Update button styles
                historyTabButtons.forEach(btn => btn.classList.remove('activo'));
                button.classList.add('activo');

                // Show/Hide content
                const targetId = button.dataset.tabTarget;
                historyTabContents.forEach(content => {
                    if (content.id === targetId) {
                        content.classList.remove('hidden');
                    } else {
                        content.classList.add('hidden');
                    }
                });
            });
        });

        // --- Function to Apply Filters (Visual Only) ---
        const applyFilters = () => {
            console.log("Aplicando filtros (visualmente)...");
            
            // Get filter values (ensure elements exist before accessing value)
            const searchType = searchTypeSelect ? searchTypeSelect.value : 'estudiante';
            const studentKey = document.getElementById('student-key-filter') ? document.getElementById('student-key-filter').value.toLowerCase() : '';
            const studentName = document.getElementById('student-name-filter') ? document.getElementById('student-name-filter').value.toLowerCase() : '';
            const articleName = document.getElementById('article-name-filter') ? document.getElementById('article-name-filter').value.toLowerCase() : '';
            const categorySelect = document.getElementById('category-filter');
            const statusSelect = document.getElementById('status-filter');
            const category = categorySelect ? categorySelect.value : 'todas'; // 'todas', 'consejeria', 'fup'
            const status = statusSelect ? statusSelect.value : 'todos'; // 'todos', 'activo', 'devuelto', 'vencido'

            let visibleCount = 0;
            const allItems = historyLoanList ? historyLoanList.querySelectorAll('.history-loan-item') : [];

            allItems.forEach(item => {
                // Assume item is visible initially
                let show = true; 
                
                // Get item data (simple text search for now)
                const itemText = item.textContent.toLowerCase();
                const itemCategoryTag = item.querySelector('.category-tag');
                const itemCategory = itemCategoryTag ? (itemCategoryTag.classList.contains('consejeria') ? 'consejeria' : 'fup') : '';
                const itemStatusTag = item.querySelector('.status-tag');
                let itemStatus = 'desconocido';
                if(itemStatusTag){
                    if(itemStatusTag.classList.contains('active')) itemStatus = 'activo';
                    else if(itemStatusTag.classList.contains('returned')) itemStatus = 'devuelto';
                    else if(itemStatusTag.classList.contains('overdue')) itemStatus = 'vencido';
                }

                // Apply Filters
                if (searchType === 'estudiante') {
                    if (studentKey && !itemText.includes(studentKey)) show = false;
                    if (studentName && !itemText.includes(studentName)) show = false;
                } else { // 'articulo'
                     if (articleName && !itemText.includes(articleName)) show = false;
                }
                
                if (category !== 'todas' && itemCategory !== category) show = false;
                if (status !== 'todos' && itemStatus !== status) show = false;

                // Show/Hide item
                if (show) {
                    item.style.display = 'grid'; // Use grid display
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

             // Update counts
             if(resultsCountSpan) resultsCountSpan.textContent = visibleCount;
             if(historyRecordCountSpan) historyRecordCountSpan.textContent = `${visibleCount} registro${visibleCount !== 1 ? 's' : ''}`;
        };

        // --- Event: Filter Form Submission ---
        historyFilterForm.addEventListener('submit', (event) => {
            event.preventDefault(); 
            applyFilters(); // Call the filter function
             // Switch to results tab automatically
             const resultsTabButton = document.querySelector('.history-tab-button[data-tab-target="history-results"]');
             if(resultsTabButton) resultsTabButton.click();
        });

        // --- Event: Click Clear Filters Button ---
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', () => {
                console.log("Limpiando filtros...");
                historyFilterForm.reset(); // Reset form fields to defaults
                // Trigger change on search type to show/hide correct fields
                if(searchTypeSelect) searchTypeSelect.dispatchEvent(new Event('change')); 
                // Re-apply filters to show all items again
                applyFilters(); 
                // Optional: Switch back to results tab if needed
                const resultsTabButton = document.querySelector('.history-tab-button[data-tab-target="history-results"]');
                 if(resultsTabButton) resultsTabButton.click();
            });
        }

        // Trigger initial filter setup 
        if(searchTypeSelect) searchTypeSelect.dispatchEvent(new Event('change'));
        // Apply filters initially to calculate initial counts
        applyFilters(); 

    } // Fin del if(historyFilterForm)

    // ==========================================================
    // LÓGICA PARA LA PÁGINA DE REPORTES (reports.html)
    // ==========================================================
    
    // Busca el formulario de configuración de reportes
    const reportConfigForm = document.getElementById('report-config-form');

    // Solo ejecuta este código si estamos en la página de reportes
    if (reportConfigForm) {
        
        // --- Selección de elementos del formulario ---
        const reportTypeSelect = document.getElementById('report-type');
        const reportWeekGroup = document.getElementById('report-week-group');
        const reportMonthGroup = document.getElementById('report-month-group');
        
        // --- Selección de elementos de la vista previa ---
        const reportPreviewCard = document.getElementById('report-preview-card');
        const previewTitle = document.getElementById('preview-title');
        const previewDesc = document.getElementById('preview-desc');
        const reportCategory = document.getElementById('report-category');

        // --- Evento: Cambiar Tipo de Reporte (Semanal/Mensual) ---
        // Muestra u oculta el input de semana o mes
        if (reportTypeSelect) {
            reportTypeSelect.addEventListener('change', () => {
                if (reportTypeSelect.value === 'semanal') {
                    if (reportWeekGroup) reportWeekGroup.classList.remove('hidden');
                    if (reportMonthGroup) reportMonthGroup.classList.add('hidden');
                } else {
                    if (reportWeekGroup) reportWeekGroup.classList.add('hidden');
                    if (reportMonthGroup) reportMonthGroup.classList.remove('hidden');
                }
            });
        }

        // --- Evento: Enviar el formulario de configuración ---
        reportConfigForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Evita que la página se recargue

            // --- Simulación de generación de vista previa ---
            
            // 1. Obtener valores de los filtros
            const tipoReporte = reportTypeSelect.options[reportTypeSelect.selectedIndex].text;
            const categoria = reportCategory.options[reportCategory.selectedIndex].text;
            let periodo = "";
            
            if (reportTypeSelect.value === 'semanal') {
                const weekVal = document.getElementById('report-week').value;
                periodo = weekVal ? `la semana de ${weekVal}` : "la semana seleccionada";
            } else {
                const monthVal = document.getElementById('report-month').value;
                periodo = monthVal ? `el mes de ${monthVal}` : "el mes seleccionado";
            }

            // 2. Poblar la tarjeta de vista previa con los datos simulados
            if(previewTitle) previewTitle.textContent = `Vista Previa: ${tipoReporte}`;
            if(previewDesc) previewDesc.innerHTML = `Mostrando datos simulados para <strong>${tipoReporte}</strong> (${periodo}) de la categoría <strong>${categoria}</strong>.`;

            // 3. Mostrar la tarjeta de vista previa
            console.log("Generando vista previa (simulación)...");
            if(reportPreviewCard) reportPreviewCard.classList.remove('hidden');
            
            // 4. Opcional: Scroll suave hacia la vista previa
            if(reportPreviewCard) {
                reportPreviewCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        // Inicializar el tipo de reporte al cargar (para ocultar el campo de mes)
        if(reportTypeSelect) reportTypeSelect.dispatchEvent(new Event('change'));
    }


    // ==========================================================
    // LÓGICA PARA BOTONES DE REPORTE RÁPIDO (Dashboards)
    // ==========================================================
    
    // (Asegúrate de haber añadido los IDs 'quick-report-weekly' y 'quick-report-monthly' 
    // a los botones en tus archivos admin_dashboard.html y member_dashboard.html)
    
    const quickReportWeeklyBtn = document.getElementById('quick-report-weekly');
    const quickReportMonthlyBtn = document.getElementById('quick-report-monthly');

    // --- Evento: Clic en Reporte Rápido Semanal ---
    if (quickReportWeeklyBtn) {
        quickReportWeeklyBtn.addEventListener('click', () => {
            // SIMULACIÓN: Muestra una alerta
            alert("Generando Reporte Rápido Semanal (Simulación)...\n\nEn una aplicación real, esto iniciaría una descarga de PDF de la última semana completa.");
            
            // Futura lógica de backend (cuando tengas PHP):
            // window.location.href = 'backend/generar-reporte-rapido.php?tipo=semanal';
        });
    }

    // --- Evento: Clic en Reporte Rápido Mensual ---
    if (quickReportMonthlyBtn) {
        quickReportMonthlyBtn.addEventListener('click', () => {
            // SIMULACIÓN: Muestra una alerta
            alert("Generando Reporte Rápido Mensual (Simulación)...\n\nEn una aplicación real, esto iniciaría una descarga de PDF del último mes completo.");
            
            // Futura lógica de backend (cuando tengas PHP):
            // window.location.href = 'backend/generar-reporte-rapido.php?tipo=mensual';
        });
    }

    // ==========================================================
    // NUEVA LÓGICA PARA LA PÁGINA GESTIONAR USUARIOS
    // ==========================================================

    // Busca el contenedor de la lista de usuarios para saber si estamos en la página correcta
    const userListContainer = document.getElementById('user-list-container');

    // Solo ejecuta este código si estamos en la página de gestión de usuarios
    if (userListContainer) {

        // --- Selección de Elementos ---
        const addUserModal = document.getElementById('add-user-modal');
        const editUserModal = document.getElementById('edit-user-modal');
        
        const openAddUserModalBtn = document.getElementById('open-add-user-modal-btn');
        const closeAddUserModalBtn = document.getElementById('close-add-user-modal-btn');
        const closeEditUserModalBtn = document.getElementById('close-edit-user-modal-btn');
        const cancelEditUserBtn = document.getElementById('cancel-edit-user-btn');
        
        const addUserForm = document.getElementById('add-user-form');
        const editUserForm = document.getElementById('edit-user-form');

        // --- Funciones para abrir/cerrar modales (ya existen, pero las usamos) ---
        // (Asumimos que openModal y closeModal están definidas globalmente en el DOMContentLoaded)
        // const openModal = (modal) => { if (modal) modal.classList.remove('hidden'); };
        // const closeModal = (modal) => { if (modal) modal.classList.add('hidden'); };

        // --- Evento para ABRIR el modal de AGREGAR ---
        if (openAddUserModalBtn) {
            openAddUserModalBtn.addEventListener('click', () => {
                addUserForm.reset(); // Limpia el formulario
                openModal(addUserModal);
            });
        }

        // --- Eventos para CERRAR modales ---
        if (closeAddUserModalBtn) {
            closeAddUserModalBtn.addEventListener('click', () => closeModal(addUserModal));
        }
        if (closeEditUserModalBtn) {
            closeEditUserModalBtn.addEventListener('click', () => closeModal(editUserModal));
        }
        if (cancelEditUserBtn) {
            cancelEditUserBtn.addEventListener('click', () => closeModal(editUserModal));
        }

        // --- Evento para CERRAR modales haciendo clic FUERA ---
        // (Este listener se añade al window, pero solo dentro de esta página)
        window.addEventListener('click', (event) => {
            if (event.target === addUserModal) {
                closeModal(addUserModal);
            }
            if (event.target === editUserModal) {
                closeModal(editUserModal);
            }
        });

        // --- Evento: Enviar formulario de AGREGAR Usuario ---
        if (addUserForm) {
            addUserForm.addEventListener('submit', (event) => {
                event.preventDefault();
                // Aquí iría la lógica de backend
                console.log("Creando nuevo usuario (simulación)...");
                
                // Simulación visual: (Opcional, pero recomendado)
                // 1. Obtener datos del form
                const newName = document.getElementById('add-user-name').value;
                const newEmail = document.getElementById('add-user-email').value;
                const newRol = document.getElementById('add-user-rol').value;
                const rolText = newRol === 'admin' ? 'Administrador' : 'Miembro';

                // 2. Crear el nuevo elemento de la lista (simulando ID)
                const newId = Math.floor(Math.random() * 1000) + 5; // ID aleatorio
                const newUserItem = document.createElement('div');
                newUserItem.classList.add('user-manage-item');
                newUserItem.dataset.id = newId;
                newUserItem.dataset.name = newName;
                newUserItem.dataset.email = newEmail;
                newUserItem.dataset.rol = newRol;
                
                newUserItem.innerHTML = `
                    <div class="col-article">
                        <span class="item-name">${newName}</span>
                        <span class="item-id">ID: ${newId}</span>
                    </div>
                    <div class="col-desc">${newEmail}</div>
                    <div class="col-cat">
                        <span class="rol-badge ${newRol}">${rolText}</span>
                    </div>
                    <div class="col-status">
                        <span class="status-badge available">Activo</span>
                    </div>
                    <div>Recién Creado</div>
                    <div class="col-actions">
                        <button class="action-btn small-btn edit-user-btn">Editar</button>
                        <button class="action-btn small-btn red deactivate toggle-active-btn">Desactivar</button>
                    </div>
                `;
                
                // 3. Añadirlo a la lista
                userListContainer.appendChild(newUserItem);

                closeModal(addUserModal);
            });
        }

        // --- Evento: Enviar formulario de EDITAR Usuario ---
        if (editUserForm) {
            editUserForm.addEventListener('submit', (event) => {
                event.preventDefault();
                
                // 1. Obtener datos del form
                const id = document.getElementById('edit-user-id').value;
                const newName = document.getElementById('edit-user-name').value;
                const newRol = document.getElementById('edit-user-rol').value;
                const rolText = newRol === 'admin' ? 'Administrador' : 'Miembro';
                
                console.log(`Guardando cambios para ID ${id} (simulación)...`);

                // 2. Buscar la fila del usuario en la lista
                const userRow = userListContainer.querySelector(`.user-manage-item[data-id="${id}"]`);
                if (userRow) {
                    // 3. Actualizar datos en el dataset (para futuras ediciones)
                    userRow.dataset.name = newName;
                    userRow.dataset.rol = newRol;
                    
                    // 4. Actualizar visualmente la fila
                    userRow.querySelector('.item-name').textContent = newName;
                    const rolBadge = userRow.querySelector('.rol-badge');
                    rolBadge.textContent = rolText;
                    rolBadge.className = `rol-badge ${newRol}`; // Limpia clases y pone la nueva
                }
                
                closeModal(editUserModal);
            });
        }

        // --- Eventos para botones de la LISTA (Editar, Activar/Desactivar) ---
        userListContainer.addEventListener('click', (event) => {
            
            // --- Clic en botón EDITAR ---
            const editButton = event.target.closest('.edit-user-btn');
            if (editButton) {
                const userRow = editButton.closest('.user-manage-item');
                const data = userRow.dataset;
                
                // Poblar el formulario del modal de edición
                document.getElementById('edit-user-id').value = data.id;
                document.getElementById('edit-user-name').value = data.name;
                document.getElementById('edit-user-email').value = data.email;
                document.getElementById('edit-user-rol').value = data.rol;
                
                openModal(editUserModal);
            }

            // --- Clic en botón ACTIVAR/DESACTIVAR ---
            const toggleButton = event.target.closest('.toggle-active-btn');
            if (toggleButton) {
                const userRow = toggleButton.closest('.user-manage-item');
                const userName = userRow.dataset.name;
                const statusBadge = userRow.querySelector('.col-status .status-badge');

                // Si el botón es para DESACTIVAR
                if (toggleButton.classList.contains('deactivate')) {
                    if (confirm(`¿Estás seguro de que deseas desactivar a ${userName}?`)) {
                        // Simulación de backend
                        console.log(`Desactivando a ${userName}...`);
                        
                        // Cambiar badge de estado
                        statusBadge.textContent = 'Inactivo';
                        statusBadge.classList.remove('available');
                        statusBadge.classList.add('unavailable');
                        
                        // Cambiar el botón
                        toggleButton.textContent = 'Activar';
                        toggleButton.classList.remove('red', 'deactivate');
                        toggleButton.classList.add('green', 'activate');
                    }
                }
                // Si el botón es para ACTIVAR
                else if (toggleButton.classList.contains('activate')) {
                    // Simulación de backend
                    console.log(`Activando a ${userName}...`);

                    // Cambiar badge de estado
                    statusBadge.textContent = 'Activo';
                    statusBadge.classList.remove('unavailable');
                    statusBadge.classList.add('available');
                    
                    // Cambiar el botón
                    toggleButton.textContent = 'Desactivar';
                    toggleButton.classList.remove('green', 'activate');
                    toggleButton.classList.add('red', 'deactivate');
                }
            }
        });

    } // Fin del if(userListContainer)
});