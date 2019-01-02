import { Injectable } from '@angular/core';
import { Quote } from './Quote';

@Injectable({
    providedIn: 'root'
})
export class QuoteService {
    private quotes: Quote[];
    public index: number;

    public constructor(/*private httpClient: HttpClient*/) {
        this.quotes = [     // This should be pulled from a server at some point
            new Quote(0, new Date(2018, 11, 13), "D: Was kommt nach einer Milliarden? Trillionen?", 0, 0),
            new Quote(1, new Date(2018, 11, 13), "S: Sie haben 'y' gesagt und 'u' geschrieben.\nD: Das kommt von den Tabletten.", 5, 1),
            new Quote(5, new Date(2018, 11, 15), "S: Lachen hält mich davon ab, Selbstmord zu begehen.", 0, -1),
            new Quote(3, new Date(2018, 11, 16), "D: Auf der Ebene prüfe ich nicht. Ich bin kein Parser.", -2, 0)
        ];
    }

    public getRandomQuote(): Quote {
        let newI: number;
        let i = 0;
        while((newI = Math.floor(Math.random() * this.quotes.length)) == this.index && i < 10) i++;
        this.index = newI;
        return this.quotes[this.index];
    }
}
