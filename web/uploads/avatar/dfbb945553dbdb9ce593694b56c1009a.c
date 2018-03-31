public class Test {
 public static void main(String[] args) {
  Graphe g = new Graphe();
  for (int i=0; i<4; i++) {
  g.addSommet();
  }
  
  Sommet s0=g.getSommet(0);
  Sommet s1=g.getSommet(1);
  Sommet s2=g.getSommet(2);
  Sommet s3=g.getSommet(3);
  
  s0.addArrete(1, 3, 3);
  s0.addArrete(2, 6, 2);
  s2.addArrete(1, 4, 1);
  s1.addArrete(3, 6, 4);
  s2.addArrete(3, 1, 1);
  g.marquageGraphe();
  g.afficheSommet();
}
}


