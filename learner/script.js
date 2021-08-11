var app = angular.module("app", []);

app.controller("listController", ["$scope",
  function($scope) {
    
   $scope.employees = [{pageIndex: "randomText1",pageHeader: "This should be shown in page1"},
   {pageIndex: "randomeTexthere",pageHeader: "This should be shown in page2"}];
 
  $scope.export = function() {
 
        var pdf = new jsPDF('landscape');
    //  var pdf = new jsPDF('p','pt','a4');
      var pdfName = 'test.pdf';
   var imagesToResize = document.getElementsByTagName("img");
    for(i=0;i<imagesToResize.length;i++){
      imagesToResize[i].style.width = "100px";
      imagesToResize[i].style.height = "100px";
    }
       var options = {   pagesplit: true};

       var $divs = $('.myDivClass')   
     //jQuery object of all the myDivClass divs
       var numRecursionsNeeded = $divs.length -1;     //the number of times we need to call addHtml (once per div)
       var currentRecursion=0;
                     
       //Found a trick for using addHtml more than once per pdf. Call addHtml in the callback function of addHtml recursively.
       function recursiveAddHtmlAndSave(currentRecursion, totalRecursions){
           //Once we have done all the divs save the pdf
           if(currentRecursion==totalRecursions){
               pdf.save(pdfName);
           }else{
                currentRecursion++;
               pdf.addPage();
               //$('.myDivClass')[currentRecursion] selects one of the divs out of the jquery collection as a html element
               //addHtml requires an html element. Not a string like fromHtml.
               pdf.fromHTML($('.myDivClass')[currentRecursion], 15, 20, options, function(){
                   console.log(currentRecursion);
                   recursiveAddHtmlAndSave(currentRecursion, totalRecursions)
               });
           }
       }

       pdf.fromHTML($('.myDivClass')[currentRecursion], 15, 20, options, function(){
           recursiveAddHtmlAndSave(currentRecursion, numRecursionsNeeded);
       });
   }
  }
]);