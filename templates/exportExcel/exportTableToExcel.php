<?php 

    //Do nothing
?>

<script>

    function exportTableToExcel(tableId, filename = 'report'){
        
        console.log("clicked");
        
        var downloadLink;
        var dataType = 'application/vnd.ms-excel';
        var tableSelect = document.getElementById(tableId);
        //var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
        
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        var tableRows = tableSelect.rows;
        var tableData = "";
        
        // Get the first row 
        var firstRow = tableRows[0];
        
        // Get the title of the first column
        var firstColumnTitle = firstRow.cells[0].textContent;
        
        // Check if the title is "Action"
        if (firstColumnTitle === "Action") {
            for (var i = 0; i < tableRows.length; i++) {
                    
                    
                    var row = tableRows[i];
                    
                    if(filename === 'payments'){
                        for (var j = 1; j < (row.cells.length -1); j++) {
                            var cell = row.cells[j];
                            var input = cell.querySelector('input[type="text"]');
                            if (input) {
                                tableData += input.value;
                            } else {
                                tableData += cell.textContent || cell.innerText;
                            }
                            if (j < row.cells.length - 1) {
                                tableData += "\t"; // Add a tab character to separate cells
                            }
                        }
                    } else {
                        for (var j = 1; j < row.cells.length; j++) {
                            var cell = row.cells[j];
                            var input = cell.querySelector('input[type="text"]');
                            if (input) {
                                tableData += input.value;
                            } else {
                                tableData += cell.textContent || cell.innerText;
                            }
                            if (j < row.cells.length - 1) {
                                tableData += "\t"; // Add a tab character to separate cells
                            }
                        }
                    }
                    
                    tableData += "\n"; // Add a newline character to separate rows
                }
        } else {

        
            for (var i = 0; i < tableRows.length; i++) {
                
                
                var row = tableRows[i];
                
                if(filename === 'payments'){
                    for (var j = 0; j < (row.cells.length -1); j++) {
                        var cell = row.cells[j];
                        var input = cell.querySelector('input[type="text"]');
                        if (input) {
                            tableData += input.value;
                        } else {
                            tableData += cell.textContent || cell.innerText;
                        }
                        if (j < row.cells.length - 1) {
                            tableData += "\t"; // Add a tab character to separate cells
                        }
                    }
                } else {
                    for (var j = 0; j < row.cells.length; j++) {
                        var cell = row.cells[j];
                        var input = cell.querySelector('input[type="text"]');
                        if (input) {
                            tableData += input.value;
                        } else {
                            tableData += cell.textContent || cell.innerText;
                        }
                        if (j < row.cells.length - 1) {
                            tableData += "\t"; // Add a tab character to separate cells
                        }
                    }
                }
                
                tableData += "\n"; // Add a newline character to separate rows
            }
        }
        
        var tableHTML = encodeURIComponent(tableData);
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        // Specify the filename
        filename = filename?filename+'.xls':'excel_data.xls';
        
        // Create download link element
        downloadLink = document.createElement("a");
        
        document.body.appendChild(downloadLink);
        
        if(navigator.msSaveOrOpenBlob){
            var blob = new Blob(['\ufeff', tableHTML], {
                type: dataType
            });
            navigator.msSaveOrOpenBlob( blob, filename);
        }else{
            // Create a link to the file
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
            
            // Setting the file name
            downloadLink.download = filename;
            
            //triggering the function
            downloadLink.click();
        }
    }
    
 /*
   
    function exportTableToExcel(tableId, filename = 'report') {
        var downloadLink;
        var dataType = 'application/vnd.ms-excel';
        var tableSelect = document.getElementById(tableId);
    
        // Filter out empty rows
        var nonEmptyRows = Array.from(tableSelect.rows).filter(row => {
            for (var j = 0; j < row.cells.length; j++) {
                var cell = row.cells[j];
                var input = cell.querySelector('input[type="text"]');
                if (input && input.value.trim() !== '') {
                    return true; // Row is not empty
                } else if (cell.textContent.trim() !== '') {
                    return true; // Row is not empty
                }
            }
            return false; // Row is empty
        });
    
        if (nonEmptyRows.length === 0) {
            alert('No data to export.');
            return;
        }
    
        // Generate table data
        var tableData = "";
        for (var i = 0; i < nonEmptyRows.length; i++) {
            var row = nonEmptyRows[i];
    
            for (var j = 0; j < row.cells.length; j++) {
                var cell = row.cells[j];
                var input = cell.querySelector('input[type="text"]');
                if (input) {
                    tableData += input.value;
                } else {
                    tableData += cell.textContent || cell.innerText;
                }
                if (j < row.cells.length - 1) {
                    tableData += "\t"; // Add a tab character to separate cells
                }
            }
    
            tableData += "\n"; // Add a newline character to separate rows
        }
    
        var tableHTML = encodeURIComponent(tableData);
    
        // Specify the filename
        filename = filename ? filename + '.xls' : 'excel_data.xls';
    
        // Create download link element
        downloadLink = document.createElement("a");
    
        document.body.appendChild(downloadLink);
    
        if (navigator.msSaveOrOpenBlob) {
            var blob = new Blob(['\ufeff', tableHTML], {
                type: dataType
            });
            navigator.msSaveOrOpenBlob(blob, filename);
        } else {
            // Create a link to the file
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
    
            // Setting the file name
            downloadLink.download = filename;
    
            // Triggering the function
            downloadLink.click();
        }
    }
   */

</script>
