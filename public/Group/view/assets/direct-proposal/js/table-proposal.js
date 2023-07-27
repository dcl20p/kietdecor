(function($){
  let rowCounter = 0;

  function cloneTableRow() {
    const selectedRow = $(this).closest("tr");
    const clonedRow = $("<tr></tr>");

    const columnsToClone = [1, 2, 4];
    columnsToClone.forEach((colIndex) => { 
      const cellToClone = selectedRow.find("td").eq(colIndex).clone();
      clonedRow.append(cellToClone);
    });
    return clonedRow;
  }

  function addNewRow() {
    const newRow = cloneTableRow.call(this);
    $("#table-result").append(newRow);

    const removeButton = newRow.find(".btn-add-row");
    removeButton.text("解除");
    removeButton
      .removeClass("btn-add-row btn-success")
      .addClass("btn-danger btn-remove-row-table");

    const mainTableRowIndex = $(this).closest("tr").index();
    newRow.attr("data-main-table-row", mainTableRowIndex);

    sortTableRows();
  }

  function sortTableRows() {
    const tableRows = $("#table-result tbody tr");
    tableRows.sort(function(a, b) {
      const rowA = parseInt($(a).attr("data-main-table-row"));
      const rowB = parseInt($(b).attr("data-main-table-row"));
      return rowA - rowB;
    });
  
    $("#table-result tbody").empty().append(tableRows);
  }

  function removeTableRow() {
    const selectedRow = $(this).closest("tr");
    const mainTableRowIndex = selectedRow.data("main-table-row");

    selectedRow.remove();

    const mainTableRow = $("#table-main tbody tr").eq(mainTableRowIndex);
    mainTableRow
      .find(":checkbox")
      .prop("disabled", false)
      .prop("checked", false)
      .addClass("disabled not-allow");
    mainTableRow
      .find('.btn-add-row')
      .removeClass('disabled not-allow');
  }

  function updateCheckAllStatus () {
    const tableBody = $("#table-main tbody");
    const checkAllCheckbox = $("#checkall");
    const rowCheckboxes = tableBody.find(":checkbox");
    const disabledCheckboxes = tableBody.find(":checkbox[disabled]");
    const allDisabled = rowCheckboxes.length === disabledCheckboxes.length;
    const allChecked =  rowCheckboxes.length > 0 && 
      rowCheckboxes.length === rowCheckboxes.filter(":checked").length;
  
    checkAllCheckbox.prop("checked", allChecked);
    checkAllCheckbox.prop("disabled", allDisabled);
  }

  function checkTableMainCheckboxes() {
    const anyChecked = $("#table-main tbody :checkbox:checked:not(:disabled)").length > 0;
    $(".btn-add-all").toggleClass("disabled not-allow", !anyChecked);
  }

  function renderRowSelect (rowCounter) {
    const elCountRow = document.getElementById("number-select");
    elCountRow.textContent = rowCounter;
  }

  function limitText(elementSelector, maxLength) {
    const elements = document.querySelectorAll(elementSelector);
    elements.forEach(element => {
      const text = element.textContent;
      if (text.length > maxLength) {
        element.textContent = text.slice(0, maxLength) + "...";
      }
    });
  }
  
  limitText(".limit-text", 15);

  $(document).on("click", ".btn-add-row", function () {
    addNewRow.call(this);

    const selectedRow = $(this).closest("tr");

    selectedRow
      .find(":checkbox")
      .prop("disabled", true)
      .prop("checked", true)
      .addClass("disabled not-allow");;

    selectedRow
      .find('.btn-add-row')
      .addClass('disabled not-allow');

    rowCounter++;
    renderRowSelect(rowCounter);
    checkTableMainCheckboxes();
    updateCheckAllStatus();
  });

  $(document).on("click", ".btn-remove-row-table", function () {
    rowCounter -= 1;
    renderRowSelect(rowCounter);
    removeTableRow.call(this);
    updateCheckAllStatus();
    checkTableMainCheckboxes();
  });

  $("#table-main :checkbox").on("change", checkTableMainCheckboxes);

  $("#table-main").on("change", "tbody :checkbox", function () {
    updateCheckAllStatus();
  });

  $(".btn-add-all").on("click", function () { 
    if($(this).hasClass("disabled not-allow")) {
      return
    }
    const checkedCheckbox = $("#table-main tbody tr").has(":checkbox:checked:not(:disabled)");
    const countCheckedCheckbox = checkedCheckbox.length;

    checkedCheckbox.each(function () { 
      addNewRow.call(this);

      $(this)
        .find(":checkbox")
        .prop("disabled", true)
        .addClass("disabled not-allow");

      $(this)
        .find('.btn-add-row')
        .addClass('disabled not-allow');
    });

    rowCounter += countCheckedCheckbox;
    renderRowSelect(rowCounter);
    updateCheckAllStatus()
    checkTableMainCheckboxes();
  });

  $("#checkall").on("click", function () {
    const isChecked = this.checked;
    const checkboxes = $("#table-main :checkbox:not(:disabled)");
    checkboxes.prop("checked", isChecked);
  });

  checkTableMainCheckboxes();
  updateCheckAllStatus ();
})(jQuery);

