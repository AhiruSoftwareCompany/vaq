| Call           | HTTP-Method | Path        | Parameter (Body) | Return-Value | HTTP-Code       |
| -------------- | ----------- | ----------- | ---------------- | ------------ | --------------- |
| getRandomQuote | GET         | /quote      |        -         | Quote        | 200 / 404       |
| refreshRating  | PUT         | /quote/{id} | {-1, 1}          |      -       | 200 / 201 / 400 |
