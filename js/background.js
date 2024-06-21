let canvas;
let ctx;
let tab;
let nbPoint;

document.addEventListener('DOMContentLoaded', function() {
    canvas = document.getElementById("bg");
    nbPoint = Math.floor((window.innerWidth * window.innerHeight) / 15000);
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    ctx = canvas.getContext("2d");

    tab = getPoints(nbPoint);
    draw();
});

window.addEventListener("resize",() =>{
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
})

function draw() {
    ctx.beginPath();
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.closePath();
    tab.forEach(function (e){
        e.bouger();
        tab.forEach(function(autre){
            if (e !== autre) {
                let distance = Point.getDistance(e, autre);
                if (Point.distanceValide(distance)) {
                    let opacite = 1 - (distance / Point.distanceMini);
                    ctx.beginPath();
                    ctx.strokeStyle = `rgba(125, 125, 125, ${opacite})`;
                    ctx.moveTo(e.getX(), e.getY());
                    ctx.lineTo(autre.getX(), autre.getY());
                    ctx.stroke();
                    ctx.closePath();
                }
            }
        })
        e.dessiner();
    })
    requestAnimationFrame(draw);
}
function getPoints(nbPoint){
    let tab = new Array(nbPoint);
    for (let i = 0; i < nbPoint; i++){
        let p = new Point(getRandomX(), getRandomY(), canvas, ctx);
        tab.push(p);
    }
    return tab;
}

function getRandomX(){
    return Math.floor(Math.random() * (canvas.width-50)) + 20;
}

function getRandomY(){
    return Math.floor(Math.random() * (canvas.height-50)) + 20;
}


class Point{
    x;
    y;
    canvas;
    ctx;
    vitesseX;
    vitesseY;
    taille;
    static distanceMini = 150;
    constructor(x, y, canvas, ctx) {
        this.x = x;
        this.y = y;
        this.canvas = canvas;
        this.ctx = ctx;
        this.vitesseX = Math.random() * 3 - 1.5;
        this.vitesseY = Math.random() * 3 - 1.5;
        this.taille = this.getRandomTaille();
    }

    getX(){
        return this.x;
    }

    getY(){
        return this.y;
    }
    dessiner(){
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.taille, 0, Math.PI * 2);
        ctx.fillStyle = "grey";
        ctx.fill();
        ctx.closePath();
    }
    bouger(){
        this.x += this.vitesseX;
        this.y += this.vitesseY;

        if(this.x >= canvas.width){
            this.vitesseX = Math.floor(Math.random() * (1)+0.5) * -1;
        }
        if(this.x < 1) {
            this.vitesseX = Math.floor(Math.random() * (1)+0.5)
        }

        if(this.y >= canvas.height){
            this.vitesseY = Math.floor(Math.random() * (1)+0.5) * -1;
        }

        if(this.y < 1) {
            this.vitesseY = Math.floor(Math.random() * (1)+0.5);
        }
    }

    getRandomTaille(){
        return Math.floor(Math.random() * 2) + 1;
    }
    static distanceValide(distance){
        return distance < Point.distanceMini;
    }

    static getDistance(p1, p2){
        return Math.sqrt(Math.pow((p2.getX() - p1.getX()), 2) + Math.pow((p2.getY() - p1.getY()), 2));
    }
}


