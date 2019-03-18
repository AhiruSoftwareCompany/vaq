import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

import { Quote } from '../models/quote';
import { User } from '../models/user';

@Injectable({
    providedIn: 'root'
})
export class DataService {
    private url = "/rest/vaq/";

    public constructor(private httpClient: HttpClient) { }

    public getRandomQuote(origins: string[]): Promise<Quote> {
        let url = this.url + "quote";
        origins.forEach(function(origin) {
            url += "/" + origin;
        })
        return this.httpClient.get(url)
        .toPromise()
        .then((response: Quote) => {
            const quote: Quote = new Quote(response.id, response.date, response.origin, response.body, response.rating, response.vote);
            return quote;
        })
        .catch(this.handleError);
    }

    public refreshRating(quote: Quote): Promise<number> {
        return this.httpClient.put(this.url + "quote/" + quote.id, quote.vote)
        .toPromise()
        .then((response: number) => {
            return response;
        });
    }

    public login(user?: User): Promise<User> {
        return this.httpClient.post(this.url + "login", user)
        .toPromise()
        .then((response: User) => {
            const user: User = new User(response.name, response.pwd, response.origins);
            return user;
        })
        .catch(this.handleError);
    }

    private handleError(error: any): Promise<any> {
        return Promise.reject(error.message || error);
    }
}
