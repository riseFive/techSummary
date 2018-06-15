#define MAX_TREE_SIZE 100
typedef char ElemType;

//孩子节点
typedef struct CTNode{
 int child;     //孩子节点下标
 struct CTNode *next;  //指向下一个孩子节点的指针
} *ChildPtr;

//表头结构
typedef struct{
 ElemType data;  //存放在树中的终点的数据
 int parent;     //存放双亲的下标
 ChildPtr firstChild;  //指向第一个孩子的指针
}CTBox;

//树结构
typedef struct{
CTBox nodes[MAX_TREE_SIZE]; //节点数组
int r,n; //r 根节点 n节点个数
}
<img src="http://orvwtnort.bkt.clouddn.com/201721343/1528957773703.png" width="228"/>
<img src="http://orvwtnort.bkt.clouddn.com/201721343/1528960548321.png" width="253"/>

