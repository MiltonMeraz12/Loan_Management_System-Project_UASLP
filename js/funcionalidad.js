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
        });
    }

    // --- Lógica al ENVIAR el formulario de EDITAR ---
     if (editItemForm) {
        editItemForm.addEventListener('submit', (event) => {
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
                    window.location.href = '../control/eliminar_articulo.php?id=' + itemId;
                }
            }

            // Si se hizo clic en un botón REACTIVAR (NUEVO)
            if (event.target.closest('.reactivate-btn')) {
                const button = event.target.closest('.reactivate-btn');
                const itemId = button.dataset.id;
                const itemRow = button.closest('.inv-manage-item');
                const itemName = itemRow.querySelector('.item-name').textContent;

                const confirmed = confirm(`¿Deseas reactivar el artículo "${itemName}"? Volverá a estar disponible para préstamos.`);

                if (confirmed) {
                    window.location.href = '../control/reactivar_articulo.php?id=' + itemId;
                }
            }
        });
    }

    // ==========================================================
    // LÓGICA PARA LA PÁGINA REGISTRAR NUEVO PRÉSTAMO
    // ==========================================================

    const registerLoanForm = document.getElementById('register-loan-form');

    // Solo ejecuta esta lógica si el formulario existe en la página actual
    if (registerLoanForm) {
        
        // Selectores de Categoría y Artículo
        const categorySelect = document.getElementById('loan-category');
        const loanItemSelect = document.getElementById('loan-item');
        
        // Elementos visuales (Tablas laterales)
        const availableItemsTitle = document.getElementById('available-items-title');
        const itemCountBadge = document.getElementById('item-count'); 
        const itemsListConsejeria = document.getElementById('items-list-consejeria');
        const itemsListFup = document.getElementById('items-list-fup');
        
        // Inputs del Estudiante
        const studentKeyInput = document.getElementById('student-key');
        const studentNameInput = document.getElementById('student-name');
        const studentPhoneInput = document.getElementById('student-phone');
        const studentFacultySelect = document.querySelector('select[name="studentFaculty"]'); // Nuevo selector de Facultad
        
        // Inputs del Proxy
        const proxyCheckbox = document.getElementById('proxy-requester-checkbox');
        const proxyDetailsSection = document.getElementById('proxy-details-section');
        const proxyNameInput = document.getElementById('proxy-name');
        const proxyPhoneInput = document.getElementById('proxy-phone');

        // --- 1. GUARDAR COPIA DE SEGURIDAD (CLAVE PARA EL ÉXITO DEL SELECT) ---
        // Guardamos todas las opciones originales que PHP generó
        // Slice(1) ignora la primera opción "Selecciona un artículo"
        const allOptionsCache = Array.from(loanItemSelect.options).slice(1);

        // --- 2. FUNCIÓN DE ACTUALIZACIÓN DE INTERFAZ ---
        const updateInterface = () => {
            const selectedCategory = categorySelect.value; // 'consejeria' o 'fup'
            let categoryName = (selectedCategory === 'consejeria') ? 'Consejería' : 'FUP';

            // A. FILTRAR EL SELECT DE ARTÍCULOS
            // Limpiamos el select (dejando solo el placeholder)
            loanItemSelect.innerHTML = '<option value="" disabled selected>Selecciona un artículo</option>';
            
            // Insertamos SOLO las opciones que coinciden con la categoría, clonándolas
            allOptionsCache.forEach(option => {
                if (option.dataset.category === selectedCategory) {
                    loanItemSelect.appendChild(option.cloneNode(true));
                }
            });

            // B. ACTUALIZAR LISTAS LATERALES (VISUAL)
            if (selectedCategory === 'consejeria') {
                if(itemsListConsejeria) itemsListConsejeria.classList.remove('hidden');
                if(itemsListFup) itemsListFup.classList.add('hidden');
                if(itemCountBadge && itemsListConsejeria) {
                    itemCountBadge.textContent = `${itemsListConsejeria.children.length} artículos`;
                }
            } else {
                if(itemsListConsejeria) itemsListConsejeria.classList.add('hidden');
                if(itemsListFup) itemsListFup.classList.remove('hidden');
                if(itemCountBadge && itemsListFup) {
                    itemCountBadge.textContent = `${itemsListFup.children.length} artículos`;
                }
            }
            if(availableItemsTitle) availableItemsTitle.textContent = `Artículos Disponibles - ${categoryName}`;
        };

        // --- 3. AUTOCOMPLETADO POR CLAVE (AJAX) ---
        studentKeyInput.addEventListener('blur', () => {
            const clave = studentKeyInput.value.trim();

            // Solo buscamos si parece una clave válida (6 dígitos)
            if (clave.length === 6) {
                document.body.style.cursor = 'wait'; // Feedback visual

                fetch(`../control/buscar_estudiante.php?clave=${clave}`)
                    .then(response => response.json())
                    .then(result => {
                        document.body.style.cursor = 'default';

                        if (result.success && result.data) {
                            console.log("Estudiante encontrado:", result.data);
                            
                            // Rellenar campos automáticamente
                            studentNameInput.value = result.data.nombre_completo;
                            studentPhoneInput.value = result.data.telefono;
                            
                            // Seleccionar la facultad correcta
                            if (result.data.facultad && studentFacultySelect) {
                                studentFacultySelect.value = result.data.facultad;
                            }
                            
                            // Efecto visual de éxito (parpadeo verde)
                            studentNameInput.style.backgroundColor = "#e8f5e9";
                            setTimeout(() => { studentNameInput.style.backgroundColor = ""; }, 1000);
                        }
                    })
                    .catch(error => {
                        console.error("Error al buscar estudiante:", error);
                        document.body.style.cursor = 'default';
                    });
            }
        });

        // --- 4. FORMATO DE TEXTO (Solo letras para nombres) ---
        const formatName = (input) => {
            let value = input.value;
            value = value.replace(/[^a-zA-ZñÑ\s]/g, '');
            value = value.toLowerCase().replace(/\b[a-zñ]/g, char => char.toUpperCase());
            value = value.replace(/\s+/g, ' ');
            input.value = value;
        };

        studentNameInput.addEventListener('input', () => formatName(studentNameInput));
        proxyNameInput.addEventListener('input', () => formatName(proxyNameInput));

        // --- 5. EVENTO DE CAMBIO DE CATEGORÍA ---
        categorySelect.addEventListener('change', updateInterface);

        // --- 6. EVENTO DE PROXY (CHECKBOX) ---
        proxyCheckbox.addEventListener('change', () => {
            if (proxyCheckbox.checked) {
                proxyDetailsSection.classList.remove('hidden');
                proxyNameInput.required = true;
                proxyPhoneInput.required = true;
            } else {
                proxyDetailsSection.classList.add('hidden');
                proxyNameInput.required = false;
                proxyPhoneInput.required = false;
                proxyNameInput.value = ''; 
                proxyPhoneInput.value = '';
            }
        });

        // --- 7. EVENTO SUBMIT (VALIDACIONES FINALES) ---
        registerLoanForm.addEventListener('submit', (event) => {
            
            // Forzar formato final de nombres
            formatName(studentNameInput);
            if(proxyCheckbox.checked) formatName(proxyNameInput);

            // Validar Clave (6 dígitos)
            if (!/^\d{6}$/.test(studentKeyInput.value)) {
                event.preventDefault();
                alert('Error: La Clave debe tener 6 dígitos.');
                studentKeyInput.focus();
                return; 
            }

            // Validar Teléfono (10 dígitos)
            if (!/^\d{10}$/.test(studentPhoneInput.value)) {
                event.preventDefault();
                alert('Error: El Teléfono debe tener 10 dígitos.');
                studentPhoneInput.focus();
                return;
            }

            // Validar Proxy Phone
            if (proxyCheckbox.checked && !/^\d{10}$/.test(proxyPhoneInput.value)) {
                event.preventDefault();
                alert('Error: El Teléfono del Solicitante debe tener 10 dígitos.');
                proxyPhoneInput.focus();
                return;
            }
            
            // Validar que haya artículo seleccionado
            if (!loanItemSelect.value) { 
                event.preventDefault();
                alert('Error: Debes seleccionar un Artículo.');
                loanItemSelect.focus();
                return;
            }
        });

        // --- INICIALIZACIÓN ---
        updateInterface(); 

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
    // LÓGICA PARA LA PÁGINA DE REPORTES (reports.php)
    // ==========================================================
    
    // Busca el formulario de configuración de reportes
    const reportConfigForm = document.getElementById('report-config-form');

    // Solo ejecuta este código si estamos en la página de reportes
    if (reportConfigForm) {
        
        // --- Selección de elementos del formulario ---
        const reportTypeSelect = document.getElementById('report-type');
        const reportWeekGroup = document.getElementById('report-week-group');
        const reportMonthGroup = document.getElementById('report-month-group');
        
        // Seleccionamos los INPUTS reales (no los grupos div) para cambiar el required
        const reportWeekInput = document.getElementById('report-week');
        const reportMonthInput = document.getElementById('report-month');

        // --- Evento: Cambiar Tipo de Reporte (Semanal/Mensual) ---
        if (reportTypeSelect) {
            reportTypeSelect.addEventListener('change', () => {
                if (reportTypeSelect.value === 'semanal') {
                    // Mostrar Semana / Ocultar Mes
                    if (reportWeekGroup) reportWeekGroup.classList.remove('hidden');
                    if (reportMonthGroup) reportMonthGroup.classList.add('hidden');
                    
                    // CORRECCIÓN CRÍTICA: Gestionar el atributo 'required'
                    // Si es semanal, la semana es obligatoria, el mes no.
                    if (reportWeekInput) reportWeekInput.required = true;
                    if (reportMonthInput) reportMonthInput.required = false;
                    
                } else {
                    // Ocultar Semana / Mostrar Mes
                    if (reportWeekGroup) reportWeekGroup.classList.add('hidden');
                    if (reportMonthGroup) reportMonthGroup.classList.remove('hidden');
                    
                    // CORRECCIÓN CRÍTICA: Gestionar el atributo 'required'
                    // Si es mensual, la semana NO es obligatoria, el mes sí.
                    if (reportWeekInput) reportWeekInput.required = false;
                    if (reportMonthInput) reportMonthInput.required = true;
                }
            });
        }

        // Inicializar el estado al cargar la página
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
            window.open('../control/generar_reporte.php?tipo=semanal', '_blank');
        });
    }

    // --- Evento: Clic en Reporte Rápido Mensual ---
    if (quickReportMonthlyBtn) {
        quickReportMonthlyBtn.addEventListener('click', () => {
            window.open('../control/generar_reporte.php?tipo=mensual', '_blank');
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
                        const userId = userRow.dataset.id;
                        window.location.href = '../control/cambiar_estado_usuario.php?id=' + userId + '&estado=inactivo';
                    }
                }
                // Si el botón es para ACTIVAR
                else if (toggleButton.classList.contains('activate')) {
                    const userId = userRow.dataset.id;
                    window.location.href = '../control/cambiar_estado_usuario.php?id=' + userId + '&estado=activo';
                }
            }
        });

    } // Fin del if(userListContainer)

    // ==========================================================
    // NUEVA LÓGICA PARA MODAL DE DETALLES EN HISTORIAL
    // ==========================================================
    
    const historyListContainer = document.getElementById('history-loans-list');
    const historyModal = document.getElementById('history-details-modal');
    const closeHistoryBtn = document.getElementById('close-history-modal-btn');
    const okHistoryBtn = document.getElementById('ok-history-modal-btn');

    if (historyListContainer && historyModal) {
        
        // Evento Delegado: Clic en "Ver Detalles"
        historyListContainer.addEventListener('click', (event) => {
            const btn = event.target.closest('.view-history-details-btn');
            if (!btn) return;

            const item = btn.closest('.history-loan-item');
            const data = item.dataset;

            // Llenar datos
            document.getElementById('hist-id').textContent = data.loanId;
            document.getElementById('hist-article').textContent = data.article;
            document.getElementById('hist-category').textContent = data.category;
            document.getElementById('hist-status').textContent = data.status;
            document.getElementById('hist-registered-by').textContent = data.registeredBy;
            
            document.getElementById('hist-loan-date').textContent = data.loanDate;
            document.getElementById('hist-due-date').textContent = data.dueDate;
            document.getElementById('hist-return-date').textContent = data.returnDate;

            document.getElementById('hist-student').textContent = data.student;
            document.getElementById('hist-key').textContent = data.key;
            document.getElementById('hist-phone').textContent = data.phone;
            document.getElementById('hist-id-type').textContent = data.idType;

            // Proxy
            const proxySec = document.getElementById('hist-proxy-section');
            if (data.proxyName) {
                document.getElementById('hist-proxy-name').textContent = data.proxyName;
                document.getElementById('hist-proxy-phone').textContent = data.proxyPhone;
                proxySec.classList.remove('hidden');
            } else {
                proxySec.classList.add('hidden');
            }

            // Comentarios
            document.getElementById('hist-com-loan').textContent = data.commentsLoan || 'Ninguno';
            document.getElementById('hist-com-return').textContent = data.commentsReturn || 'Ninguno';

            // Abrir Modal (usando la función global openModal si existe, o manual)
            historyModal.classList.remove('hidden');
        });

        // Cerrar Modal
        const closeHModal = () => historyModal.classList.add('hidden');
        if(closeHistoryBtn) closeHistoryBtn.addEventListener('click', closeHModal);
        if(okHistoryBtn) okHistoryBtn.addEventListener('click', closeHModal);
        
        window.addEventListener('click', (e) => {
            if (e.target === historyModal) closeHModal();
        });
    }
});