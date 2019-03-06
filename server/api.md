| Call           | HTTP-Method | Path               | Parameter (Body) | Return-Value        | HTTP-Code       |
| -------------- | ----------- | ------------------ | ---------------- | ------------------- | --------------- |
| getRandomQuote | GET         | /quote(/{origin})* | { }              | Quote               | 200 / 400 / 404 |
| refreshRating  | PUT         | /quote/{id}        | {-1, 1}          | diff: number        | 200 / 201 / 400 |
| login          | POST        | /login             | User             | User / null         | 200 / 403       |
