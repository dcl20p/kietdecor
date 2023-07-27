(function($){
    function handleCheckboxGroup(group) { 
        var groupSelector = ".cbx-checkbox-group-" + group;
        var groupCheckbox = $(groupSelector);
        var parentCheckbox = $('#group_' + group);
        groupCheckbox.on("click", function() {
            var totalGroupCheckbox = groupCheckbox.length;
            var totalGroupSelected = $(groupSelector + ":checked").length;

            parentCheckbox.prop("checked", totalGroupSelected === totalGroupCheckbox);
        })
    }
    $(".cbx-checkbox-group").on("click", function () {
		var selectedGroup = $(this).attr('data-group');
		$(".cbx-checkbox-group-" + selectedGroup).prop(
			"checked",
			$(this).prop("checked")
		);

		//selected = getSelectedCheckbox();
	});

    $(".cbx-checkbox").on("click", function () { 
        $(this).prop("checked", $(this).prop("checked"));

        var group = $(this).attr("data-group");
        handleCheckboxGroup(group);
    });

    function isAnyCheckboxChecked(groupSelector) {
			const checkboxes = $(groupSelector + ' input[type="checkbox"]');
			return checkboxes.is(":checked");
		}

		function hasMultipleChildren(listId) {
			const list = document.getElementById(listId);
			const topLevelItems = $(list).find('> li:not(:has(> li))');
			return topLevelItems.length > 1;
		}

		function getDataFilter() {
			var form = document.getElementById('form-filter-nav');
			var formData = new FormData(form);
			$.ajax({
				type: 'POST',
				url: '__api_get_filter__',
				dataType: 'JSON',
				data: formData,
				processData: false,
				contentType: false
			})
			.done(function(rs){
				if (rs.success) {
					$('.text-number').text(rs.data);
				}
			});
		}

    function handleCheckboxList(
		checkboxListSelector,
		resultSelector,
		titleSelector,
		isGroupCheckbox = false
	) { 
        const checkedResult = [];
        const anyChecked = isAnyCheckboxChecked('.' + checkboxListSelector);
        if (anyChecked) { 
          if (isGroupCheckbox) { 
            $('.' + checkboxListSelector + " .el-group-checkbox").each(function () { 
							const groupCheckbox = $(this);
							const childCheckboxes = $(this).find(".cbx-checkbox");
							const checkedChildCheckboxes = childCheckboxes.filter(":checked");
							const isGroupChecked = childCheckboxes.length === childCheckboxes.filter(":checked").length;
                    
              if (checkedChildCheckboxes.length == 0) {
								groupCheckbox.find(".cbx-checkbox-group-allow:checked").each(function() {
									const label = $(this).val();
									const nameForm = $(this).attr("name");
									const idForm = $(this).attr("id");
									checkedResult.push(`<div class="checkbox" data-scroll="${idForm}">
										<input type="checkbox" class="not-allow" name="${nameForm}" value="${label}" checked/>
										<label class="checkbox__label">${label}</label>
									</div>`);
								});
							} else {
								const labelParent = groupCheckbox
									.find(".cbx-checkbox-group")
									.closest(".checkbox")
									.find("label");
								const parentId = groupCheckbox
									.find(".cbx-checkbox-group")
									.attr("id");
								
								const groupCheckedResult = checkedChildCheckboxes.map(function () { 
									const labelText = $(this).next("label").text();
									const valChecked = $(this).val();
												const nameForm = $(this).attr("name");
										return `<li data-scroll="${parentId}">
										<div class="checkbox pt-0">
											<input type="checkbox" class="not-allow" name="${nameForm}" value="${valChecked}" checked>
											<label class="checkbox__label">${labelText}</label>
										</div>
									</li>`;
								}).get().join("");

								if (childCheckboxes.length > 1) { 
									const groupCheckedResultHTML = `<div class="checkbox pt-0">
										<input type="checkbox"  ${isGroupChecked ? "checked" : ""} disabled>
										<label class="checkbox__label">${labelParent.text()}</label>
									</div>
									<ul class="pl-4 collapse custom-collapse" id="el-${parentId}-result">${groupCheckedResult}</ul>
									<div class="text-center">
										<a href="javascript:void(0)" data-toggle="collapse" class="btn-view-more collapsed" 
											data-target="#el-${parentId}-result" aria-expanded="false" 
											aria-controls="el-${parentId}-result"></a>
									</div>`;

									checkedResult.push(`<li>${groupCheckedResultHTML}</li>`);
								} else {
									checkedResult.push(groupCheckedResult);
								};
          		}
            });
          } else {
						const inputEl = '.' + checkboxListSelector + ' input[type="checkbox"]';
						$(inputEl).each(function () {
							if ($(this).is(":checked")) {
								const labelText = $(this).next("label").text();
								const valChecked = $(this).val();
								const nameForm = $(this).attr("name");
								const idFormCheck = $(this).attr("id");

								const itemHTML = `<li data-scroll="${idFormCheck}">
									<div class="checkbox pt-0">
										<input type="checkbox" class="not-allow" name="${nameForm}" value="${valChecked}" checked>
										<label class="checkbox__label">${labelText}</label>
									</div>
								</li>`;
								checkedResult.push(itemHTML);
							}
						});
					}

					if (checkedResult.length > 0) { 
						const idFilterResult = resultSelector.replace("#", "") + "-group";
						const checkedResultHTML = `<ul class="custom-collapse show" id="${idFilterResult}">${checkedResult.join("")}</ul>`;
						const toggleReadMoreHTML = `<a href="javascript:void(0)" class="toggle-readmore" data-toggle="collapse" data-target="#${idFilterResult}" aria-expanded="false" aria-controls="${idFilterResult}">
							<span class="read-less">
								<span>閉じる</span>
								<i class="zmdi zmdi-chevron-up"></i>
							</span>
							<span class="read-more">
								<span>もっと見る</span>
								<i class="zmdi zmdi-chevron-down"></i>
							</span>
						</a>`;

						const titleText = titleSelector != "" 
							? $('.' + titleSelector).text()
							: "";

						const resultHTML = `<label class="title-element">${titleText}</label>${checkedResultHTML}${toggleReadMoreHTML}`;	
						$(resultSelector).html(resultHTML).removeClass('d-none');
						const hasMultiple = hasMultipleChildren(idFilterResult);
						const btnReadMore = $(resultSelector).find('.toggle-readmore');
						if (hasMultiple) { 
							const listItems = $(`#${idFilterResult}`).find('li:not(li li):not(:empty)');
							listItems.slice(1).addClass('d-none');
							btnReadMore.removeClass('d-none').click(function() {
								listItems.slice(1).toggleClass('d-none');
								$(this).toggleClass('collapsed');
							});
						} else {
							btnReadMore.addClass('d-none');
						}
					} else {
						$(resultSelector).empty();
					}
        } else {
					$(resultSelector).empty().addClass('d-none');
				}
    }

    $('.filter-container').on('change', function(e) { 
      var target = $(e.target);
			if (target.is('input[type="checkbox"]')) { 
				var elTarget = target.attr('data-target');
				var elParent = target.closest('ul[class^="list-"]');
				var classNameParent = elParent.attr('class');
				var classLabel = "";

				classLabel = elParent.closest('.filter-item').find('.filter-title').attr('class');
				if (typeof classLabel != "undefined")
					classLabel = classLabel.replace('filter-title', '').trim();
				else classLabel = "";

				var isGroupCheckbox;;
				switch(elTarget) {
					case "#result-region":
						isGroupCheckbox = true;
						break;
					case "#result-skill":
						isGroupCheckbox = true;
						break;
					case "#result-exclude":
						isGroupCheckbox = true;
						break;
					default:
						break;
				}
				handleCheckboxList(classNameParent, elTarget, classLabel, isGroupCheckbox);
				getDataFilter()
			}
    });
})(jQuery);