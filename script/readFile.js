
if (window.File && window.FileReader && window.FileList && window.Blob) {
    function showFile(file) { //if a file is selected

        
            var preview = document.getElementById('show-text'); //get div

            if (file == null) {
                var file = document.querySelector('input[type=file]').files[0]; //get file  
                
            } else {

            }

            var fileName = file.name;
            var pieces = fileName.split(/[\s.]+/);
            var fileExt = pieces[pieces.length-1];
            console.log(fileExt);

            var reader = new FileReader(); //add new Filereader

            if (fileExt == "CSV" || fileExt == "csv") { //has to be a .csv File

                reader.onload = function(event) {

                    
                    var text = event.target.result; //get Filetext

                    fileName = file.name;
                    fileName = fileName.split(".");
                    fileName = fileName[0] + ".ics";

                    buildJSON(text);

                }

                preview.innerHTML = "";

            }else if(fileExt == "xls"){
                preview.innerHTML = "<span class='error'>.xls files are not beeing supported. Please use .xlsx files instead!</span>"; //error
            }else if(fileExt == "xlsx"){
                readExcel(file);
            }else {
                preview.innerHTML = "<span class='error'>It doesn't seem to be a .csv/.xlsx file!</span>"; //error
            }
            reader.readAsText(file, 'UTF-8'); //utf8
        
    }
} else {
    alert("Your browser is too old to support HTML5 File API"); //error
}

function readExcel(file){

    
    var ExcelToJSON = function() {

        this.parseExcel = function(file) {
          var reader = new FileReader();
  
          reader.onload = function(e) {
            var data = e.target.result;
            var workbook = XLSX.read(data, {
              type: 'binary'
            });
            workbook.SheetNames.forEach(function(sheetName) {
              // Here is your object
              var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
              var json_object = JSON.stringify(XL_row_object);
              console.log(JSON.parse(json_object));
              jQuery( '#xlx_json' ).val( json_object );
            })
          };
  
          reader.onerror = function(ex) {
            console.log(ex);
          };
  
          reader.readAsBinaryString(file);
        };
    };

    console.log(file);
    
    var xl2json = new ExcelToJSON();
    xl2json.parseExcel(file);
}
