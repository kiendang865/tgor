<php ?
<!DOCTYPE html>
<html lang="en">
  <head>
  </head>
  <body>
    <div class="container">
    <form action="{{route('import-booking')}}" method="post" enctype="multipart/form-data">
    @csrf
    <input type="file" value="" name="file_excel" multiple >
    <button type="submit">Submit</button>
    </form>
    </div>
  </body>
</html>
