function [J, grad] = cofiCostFunc(params, Y, R, num_users, num_movies, ...
                                  num_features, lambda)
%COFICOSTFUNC Funcion de coste del filtrado colaborativo
%   [J, grad] = COFICOSTFUNC(params, Y, R, num_users, num_movies, ...
%   num_features, lambda) devuelve el coste y el gradiente
%   del problema de filtrado colaborativo

% Extrae las matrices U y W de params
X = reshape(params(1:num_movies*num_features), num_movies, num_features);
Theta = reshape(params(num_movies*num_features+1:end), ...
                num_users, num_features);

            
% Debes generar los siguientes valores correctamente
J = 0;
X_grad = zeros(size(X));
Theta_grad = zeros(size(Theta));

% ====================== TU CODIGO AQUI ======================
supuesto = X*Theta';
error = (supuesto - Y).*R;
J= (1/2)*sum(sum((error).^2));

X_grad = zeros(size(X));
Theta_grad = zeros(size(Theta));

reg_factor = sum(Theta(:).^2)+sum(X(:).^2);

J = J + lambda * reg_factor/2;

X_grad = error*Theta + lambda*X;

Theta_grad = error'X + lambda*Theta;

grad = [X_grad(:); Theta_grad(:)];

end
