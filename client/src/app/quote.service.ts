import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Quote } from './Quote';

@Injectable({
    providedIn: 'root'
})
export class QuoteService {
    private url = "/rest/vaq/";

    public constructor(private httpClient: HttpClient) { }

    public getRandomQuote(): Promise<Quote> {
        return this.httpClient.get(this.url + "quote")
        .toPromise()
        .then((response: Quote) => {
            const quote: Quote = new Quote(response.id, response.date, response.body, response.rating, response.vote);
            
            return quote;
        })
        .catch(this.handleError);
    }

    private handleError(error: any): Promise<any> {
        return Promise.reject(error.message || error);
    }
}
