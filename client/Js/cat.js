document.addEventListener('DOMContentLoaded', init);

const BASE_URI = 'http://localhost:8000/kahuna/api/';

let products = [];  //array_fun! :: /Product.php > load()  ~~>  /[] + loadProducts()  ~~> PostMan GETproductLIST[] ~~> /[]

// lets load [whatever is here] before load the PG === L2
function init() {
            loadProducts();
            bindAddProduct();
}


function loadProducts() { //loadProducts endpoint
            fetch(`${BASE_URI}product`, {
                    mode: 'cors',
                    method: 'GET'
            })
        
            .then(res => res.json())
            .then(res => {
                products = res.data;
                //result is: product is loaded into /[] !

                displayProducts();
            })
            .catch(err => console.error(err)); // fetch].[catch
}

//see in CONSOLE what problem isthere

function displayProducts() {
    let html = '';

        if (products.lenght === 0) {
                        html = '<p>You have no Turtle yet!</p>';
        } else {
            html = '<table><thead>';
            html += '<tr> <th>Serial</th> <th>Name</th> <th>Warranty</th> </tr>';
            html += '</thead></tbody>';

            for (const product of products) {
                    html += '<tr>';
                        html += `<td>${product.serial}</td>`;
                        html += `<td>${product.name}</td>`;
                        html += `<td>${product.warranty}</td>`;
                    html += '</tr>';
            }
            html += '</tbody></table>';
        }
        document.getElementById('productList').innerHTML = html;  //INDEX.HTML~26
}


function bindAddProduct() {
    document.getElementById('productForm').addEventListener('submit', (evt) => {

            evt.preventDefault();
            productData =  new FormData(document.getElementById('productForm'));

            fetch(`${BASE_URI}product`, {
                mode: 'cors',
                method: 'POST',
                body: productData
        })
        .then(loadProducts)
        .catch(err => console.error(err)); // fetch].[catch azert, hogy lassam ha hiba van

    });
}

// what is this, can DO with ~~PhP~~ elegant 'PageRefresh' 8)