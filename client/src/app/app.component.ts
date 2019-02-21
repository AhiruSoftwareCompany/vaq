import { Component, OnInit } from '@angular/core';
import { QuoteService } from './quote.service';
import { Quote } from './Quote';

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
    public currentQuote: Quote = null;
    public origins: String[];

    public constructor(
        private quoteService: QuoteService) {
    }

    public ngOnInit(): void {
        this.getOrigins();
        this.getNewQuote();
    }

    public getNewQuote(): void {
        this.quoteService.getRandomQuote()
        .then(quote => {
            this.currentQuote = quote;
        });
    }

    public refreshRating(vote: number): void {
        this.currentQuote.vote = vote;
        let ratingAN = Number(this.currentQuote.rating);
        this.quoteService.refreshRating(this.currentQuote)
        .then(diff => {
            this.currentQuote.rating = ratingAN + diff;
        });
    }

    public getOrigins(): void {
        this.quoteService.getOrigins()
        .then(origins => {
            this.origins = origins;
        });
    }
}
