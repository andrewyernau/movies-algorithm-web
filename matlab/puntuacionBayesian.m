function pi = puntuacionBayesian(ni,ri,N,R)
  %%N numero total de pelis
  %%R puntuación media de todas las pelis
  %%ni numero de puntuaciones de la peli i
  %%ri puntuacion media de la pelicula i
  pi=(N*R+ni*ri)/(N+ni)
end
