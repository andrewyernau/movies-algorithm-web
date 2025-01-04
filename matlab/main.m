[Y, R, movieList] = getData();
num_movies = size(Y, 1);
pi = zeros(num_movies, 1);

N = num_movies;
total_scores = sum(Y(:));
total_ratings = sum(R(:));
R_global = total_scores / total_ratings;

for i = 1:num_movies
    ni = sum(R(i, :));
    if ni > 0
        ri = sum(Y(i, :)) / ni;
    else
        ri = 0;
    end
    pi(i) = puntuacionBayesian(ni, ri, N, R_global);
end
updateRatings(pi, movieList);


