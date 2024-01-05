Code refactored

1- I simply used the request class gloablly, because It is using all over the controller, so the better way to do that just initialize the request in the construct and use it from that variable all over the controller.

2- The variable for the repository should be repositoryName + Repository, e.g: bookingRepository this way of variable initializing will give the better understanding of the repository.

3- Remove unused variables

4- better way of getting all data from request except _token and submit button value, is $request->except(['_token', 'submit'])

5- Create test for createorUpdate User.


X- The code is written in good format or indentation but the unused variables and code make the code terrible,
I strongly prefer that is somthing is not using then there is no need to write it. 
Because compiler reads each line and the unused line of code make the response slower.
so Its my suggestion to avoid writing unused line of code.

The good part is that each function has the its return data type. 
This is my SOP that each function should have defined their return type, so the new developer or colluege get to know 
what will he expect from that function.
