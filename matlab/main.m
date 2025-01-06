% Cargamos los datos de la base de datos
[Y, R, movieList] = getData();
[num_movies, num_users] = size(Y);

%% puntuaciones Bayesianas
pi = zeros(num_movies, 1);
N = num_movies;
total_scores = sum(Y(:));
total_ratings = sum(R(:));
R_global = total_scores / total_ratings;

% Calculamos la puntuación Bayesiana para cada película
for i = 1:num_movies
    ni = sum(R(i, :));
    if ni > 0
        ri = sum(Y(i, :)) / ni;
    else
        ri = 0;
    end
    pi(i) = puntuacionBayesian(ni, ri, N, R_global);
end

% Actualizamos las puntuaciones en la base de datos
updateRatings(pi, movieList);

%%recomendación

% Normalizamos las puntuaciones
[Ynorm, Ymean] = normalizeRatings(Y, R);

% Parámetrs para  la recomendación
num_features = 50;
lambda = 0.1;

% Inicializamos las matrices de características aleatoriamente
X = randn(num_movies, num_features);
Theta = randn(num_users, num_features);

% Minimizamos la función de coste
params = [X(:); Theta(:)];
costFunction = @(p) cofiCostFunc(p, Ynorm, R, num_users, num_movies, num_features, lambda);
options = optimset('MaxIter', 100);
[theta, cost] = fmincg(costFunction, params, options);

% Recuperamos las matrices optimizadas
X = reshape(theta(1:num_movies*num_features), num_movies, num_features);
Theta = reshape(theta(num_movies*num_features+1:end), num_users, num_features);

% Generamos predicciones para todos los usuarios
predictions = X * Theta';

% Desnormalizamos
predictions = predictions + repmat(Ymean, 1, num_users);

% Actualizamos las recomendaciones para cada usuario
for user_id = 1:num_users
    my_predictions = predictions(:, user_id);
    num_updated = updateRecommendation(my_predictions, user_id);
    fprintf('Actualizadas %d recomendaciones para el usuario %d\n', sum(num_updated), user_id);
end