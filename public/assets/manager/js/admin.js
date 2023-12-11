(function () {
	const admId = localStorage.getItem('adm_id') || '';
	const adminForm = document.querySelector('#adminForm');

	// Hide toolbar if empty
	let navExpandToolbars = document.querySelectorAll('.nav-expand-toolbar ul');
	navExpandToolbars && navExpandToolbars.forEach((navExpandToolbar) => {
		if (navExpandToolbar.children.length == 0) {
			let eleParent = navExpandToolbar.closest('.container-fluid');
			eleParent.querySelector('.btn-toolbar').style.display = "none";
		}
	});
	let navToolbars = document.querySelectorAll('.nav-toolbar');
	navToolbars && navToolbars.forEach((navToolbar) => {
		navToolbar.textContent.trim() === ""
			&& (navToolbar.style.display = "none");
	});

	/**
	 * Event back to top
	 */
	const backToTop = () => {
		window.scrollY = 0;
		document.documentElement.scrollTop = 0;
	}

	/**
	 * Event back to bottom
	 */
	const backToBottom = () => {
		window.scrollY = document.body.scrollHeight;
		document.documentElement.scrollTop = document.documentElement.scrollHeight;
	}

	let btnBackToTop = document.getElementById("back-to-top");
	let btnBackToBottom = document.getElementById("back-to-bottom");

	btnBackToTop && btnBackToTop.addEventListener("click", backToTop);
	btnBackToBottom && btnBackToBottom.addEventListener("click", backToBottom);

	// Config UI
	const KEY_UI_CONFIG = "ui_config_" + admId;
	const elDarkMode = document.querySelector('#dark-version');
	const allElColorSideBar = document.querySelectorAll('.badge-color');
	const elSideNav = document.querySelector('#sidenav-main');
	const elNavBar = document.querySelector('#navbarFixed');
	const allElBtnTypeSideNav = document.querySelectorAll('.btn-type-side-nav button');
	const elNavMini = document.querySelector('#navbarMinimize');

	const currentTime = new Date().getTime();
	let savedConfig = localStorage.getItem(KEY_UI_CONFIG);
	if (savedConfig) {
		savedConfig = JSON.parse(savedConfig);
	}
	const uiConfig = {
		colorSideBar: savedConfig ? savedConfig.colorSideBar : 'primary', // primary, dark, info, success, warning, danger
		typeSideBar: savedConfig ? savedConfig.typeSideBar : 'bg-gradient-dark', // dark, white, transparent
		navbarFixed: savedConfig ? savedConfig.navbarFixed : true,
		sideBarMini: savedConfig ? savedConfig.sideBarMini : false,
		darkMode: savedConfig ? savedConfig.darkMode : false,
	}

	/**
	 * Save config to localStorage
	 */
	const saveUIConfig = () => {
		localStorage.setItem(KEY_UI_CONFIG, JSON.stringify(uiConfig));
	};

	/**
	 * Load UI config and show view
	 */
	const loadUIConfig = () => {
		let savedUIConfig = localStorage.getItem(KEY_UI_CONFIG);
		if (savedUIConfig) {
			let parseUIConfig = JSON.parse(savedUIConfig);

			// Load dark mode
			if (parseUIConfig.darkMode) {
				document.body.classList.add('dark-version');
				elDarkMode && elDarkMode.setAttribute('checked', 'checked');
				const allElBlur = document.querySelectorAll('.text-dark');
				const exceptions = [
					'fixed-plugin-button-btt',
				];
				allElBlur && allElBlur.forEach((el) => {
					let isContains = exceptions.every(word => el.className.includes(word));
					if (!isContains) {
						el.classList.replace('text-dark', 'text-white');
					}
				});
				document.querySelectorAll('.bg-gray-100').forEach((el) => {
					el.classList.replace('bg-gray-100', 'bg-gray-600');
				});
				document.querySelectorAll('.btn-outline-dark').forEach((el) => {
					el.classList.replace('btn-outline-dark', 'btn-outline-light')
				})
			} else {
				document.body.classList.remove('dark-version');
				elDarkMode && elDarkMode.removeAttribute('checked');
			}

			// Load color side bar
			if (parseUIConfig.colorSideBar) {
				let color = parseUIConfig.colorSideBar;
				elSideNav && elSideNav.setAttribute('data-color', color);
				allElColorSideBar && allElColorSideBar.forEach((el) => {
					el.classList.remove('active');
				});
				let elColorSidebarActive = 'span.bg-gradient-' + color;
				elColorSidebarActive && document.querySelector(elColorSidebarActive).classList.add('active');
			}

			// Load type side bar
			if (parseUIConfig.typeSideBar) {
				let type = parseUIConfig.typeSideBar;
				elSideNav && elSideNav.classList.remove(
					'bg-gradient-dark',
					'bg-white',
					'bg-transparent'
				);
				elSideNav && elSideNav.classList.add(type);
				if (type == 'bg-white' || type == 'bg-transparent') {
					let allNavLink = elSideNav.querySelectorAll('.text-white');
					allNavLink && allNavLink.forEach((el) => {
						el.classList.remove('text-white');
						el.classList.add('text-dark');
					});
				} 
				allElBtnTypeSideNav && allElBtnTypeSideNav.forEach((el) => {
					el.classList.remove('active');
				});
				let elTypeSidebarActive = '.btn-' + type;
				elTypeSidebarActive && document.querySelector(elTypeSidebarActive).classList.add('active');
			}

			// Load navbar fixed
			if (!parseUIConfig.navbarFixed) {
				document.getElementById('navbarBlur').classList
					.remove(
						'position-sticky',
						'blur', 
						'shadow-blur', 
						'mt-4',
						'left-auto', 
						'top-1',
						'z-index-sticky'
					);
				elNavBar.removeAttribute('checked');
			}

			// Load side nav mini
			document.body.classList.remove('g-sidenav-hidden', 'g-sidenav-pinned');
			if (parseUIConfig.sideBarMini) {
				document.body.classList.add('g-sidenav-hidden');
				elNavMini.setAttribute('checked', 'checked');
			} else {
				if (window.deviceEnv == 4) {
					document.body.classList.add('g-sidenav-pinned');
					elNavMini.removeAttribute('checked');
				}
			}
		}
	}

	/**
	 * Set dark mode to localStorage
	 * @param {*} checked 
	 */
	const setDarkMode = (checked) => {
		uiConfig.darkMode = checked;
		saveUIConfig();
	};

	/**
	 * Set color side bar to localStorage
	 * @param {*} color 
	 */
	const setColorSideBar = (color) => {
		uiConfig.colorSideBar = color;
		saveUIConfig();
	};

	/**
	 * Set color side bar to localStorage
	 * @param {*} color 
	 */
	const setTypeSideBar = (type) => {
		uiConfig.typeSideBar = type;
		saveUIConfig();
	};

	/**
	 * Set fixed for navbar
	 * @param {*} checked 
	 */
	const setNavBarFixed = (checked) => {
		uiConfig.navbarFixed = checked;
		saveUIConfig();
	};

	/**
	 * Set fixed for navbar
	 * @param {*} checked 
	 */
	const setSideBarMini = (checked) => {
		uiConfig.sideBarMini = checked;
		saveUIConfig();
	};

	document.addEventListener("DOMContentLoaded", () =>  {
		elDarkMode && elDarkMode.addEventListener('click', (evtDM) => {
			darkMode(evtDM.currentTarget);
			setDarkMode(evtDM.currentTarget.checked);
		});
		allElColorSideBar.forEach((el) => {
			el.addEventListener('click', (evtCLB) => {
				sidebarColor(evtCLB.currentTarget);
				setColorSideBar(evtCLB.currentTarget.getAttribute('data-color'));
			});
		});
		allElBtnTypeSideNav.forEach((el) => {
			el.addEventListener('click', (evt) => {
				sidebarType(evt.currentTarget);
				setTypeSideBar(evt.currentTarget.getAttribute('data-class'));
			});
		});
		elNavBar && elNavBar.addEventListener('click', (evtNav) => {
			navbarFixed(evtNav.currentTarget);
			setNavBarFixed(evtNav.currentTarget.checked);
		});
		
		elNavMini && elNavMini.addEventListener('click', (evt) => {
			navbarMinimize(evt.currentTarget);
			setSideBarMini(evt.currentTarget.checked);

		});
		loadUIConfig();
	});
	
	if (adminForm) {
		/**
		 * handle common delete
		 * @param {*} _self 
		 */
		const handleDelete = (evt) => {
			let _self = evt.currentTarget;
			let url = _self.getAttribute('data-href');
			common.alertConfirm(evt, (value) => {
				adminForm.setAttribute('action', url);
				adminForm.setAttribute('method', 'post');
				adminForm.submit();
			});
		};
		/**
		 * Event delete multiple record
		 * @param {*} evt 
		 * @returns 
		 */
		const eventDeleteMultiple = (evt) => {
			evt.preventDefault();
			let _self = evt.target.closest('a.toolbar-delete');
			let checkedInputs = adminForm.querySelectorAll('input[name="id[]"]:checked');

			if (checkedInputs.length > 0) {
				handleDelete(evt);
			} else {
				common.showMessage(_self.getAttribute('data-rq-one') || '', 'danger');
			}
			return false;
		}
		let toolbarDelete = document.querySelectorAll('body .toolbar-delete');
		toolbarDelete && toolbarDelete.forEach((el) => {
			el.addEventListener('click', eventDeleteMultiple);
		});

		/**
		 * Event delete single record
		 * @param {*} evt 
		 */
		const eventDelete = (evt) => {
			evt.preventDefault();
			let _self = evt.currentTarget;
			let tableRow = _self.closest('tr');
			let inputChecked = tableRow.querySelector('input[name="id[]"]');

			if (inputChecked) inputChecked.checked = true;

			handleDelete(evt);
		};
		let managerDelete = document.querySelectorAll('.manage-delete');
		managerDelete && managerDelete.forEach((el) => {
			el.addEventListener('click', eventDelete);
		});

		// Check all checkbox
		let checkAll = adminForm.querySelector('#checkall');
		checkAll && checkAll.addEventListener('change', (evt) => {
			adminForm.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
				checkbox.checked = evt.currentTarget.checked;
			});
		});

		// Change status
		const eventChangeStatus = async (evt) => {
			evt.preventDefault();
			const errMsg = window.msg.went_wrong;
			let _self = evt.currentTarget,
			 	url = _self.getAttribute('data-href'),
			 	status = _self.getAttribute('data-btn-status'),
			 	token = _self.getAttribute('data-token');
	
			common.alertConfirm(evt, (value) => {
				if (value?.token) {
					token = value.token;
				}
				if (value?.success) {
					window.location.href = window.location.href;
					return;
				} else {
					Swal.fire("Oops", value.msg ?? errMsg, "error");
				}
			}, async () => {
				try {
					const response = await axios.post(url, 
						{status:status}, {
							headers: {
								'Csrf-Token': token
							}
						}
					);
					if (!response.data) {
						Swal.fire("Oops", errMsg, "error");
					}
					return response.data;
				} catch (error) {
					Swal.fire("Oops", errMsg, "error");
				}
			});
		};
		
		let managerChangeStatus = document.querySelectorAll('.change-status');
		managerChangeStatus && managerChangeStatus.forEach((link) => {
			link.addEventListener('click', eventChangeStatus);
		});
	}
})();