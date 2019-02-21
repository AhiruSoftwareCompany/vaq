export class Quote {
    public id: number;      // The unique id of the quote
    public date: Date;      // The date of the quote
    public origin: string   // The origin (category) of the quote
    public body: string;    // The actual quote, including the participants and formatting
    public rating: number;  // The global rating on the quote as sum of all votes
    public vote: number;    // The user's own vote for the quote

    public constructor(id:number, date:Date, origin:string, body:string, rating:number, vote:number) {
        this.id = id;
        this.date = date;
        this.origin = origin;
        this.body = body;
        this.rating = rating;
        this.vote = vote;
    }
}
