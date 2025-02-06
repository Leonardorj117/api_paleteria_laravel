<!DOCTYPE html>
<html>
<head>
   <title>Browse Movies</title>
</head>
<body>
<h2>Movies</h2>

@forelse ($productos as $producto)
  <p>
    Title: {{ $producto->title }}<br>
    Year: {{ $producto->year }}<br>
    Runtime: {{ $producto->runtime }}<br>
    IMDB Rating: {{ $producto->imdb['rating'] }}<br>
    IMDB Votes: {{ $producto->imdb['votes'] }}<br>
    Plot: {{ $producto->plot }}<br>
  </p>
@empty
    <p>No results</p>
@endforelse

</body>
</html>