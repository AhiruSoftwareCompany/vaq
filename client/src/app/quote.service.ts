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

    public getOrigins(): Promise<string[]> {
        return this.httpClient.get(this.url + "origins")
        .toPromise()
        .then((response: string[]) => {
            return response;
        });
    }

    private handleError(error: any): Promise<any> {
        return Promise.reject(error.message || error);
    }
}
